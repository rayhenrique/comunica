<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Release;
use App\Rules\FitsTextColumn;
use App\Support\AppVersion;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SaveReleaseAction
{
    use AuthorizesRequests;

    /** @param array<string, mixed> $data */
    public function execute(array $data, ?int $id = null): Release
    {
        $this->authorize('create', Release::class);
        $validated = Validator::make(
            $data,
            [
            'version' => ['required', 'string', 'max:32', 'regex:'.AppVersion::PATTERN, Rule::unique('releases', 'version')->ignore($id)],
            'title' => ['required', 'string', 'max:150'],
            'summary' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string', new FitsTextColumn()],
        ],
            ['version.regex' => 'Informe uma versão MAJOR.MINOR.PATCH sem zeros à esquerda.'],
            ['version' => 'versão', 'title' => 'título', 'summary' => 'resumo', 'content' => 'conteúdo']
        )->validate();

        return DB::transaction(function () use ($validated, $id): Release {
            $release = $id ? Release::lockForUpdate()->findOrFail($id) : new Release();
            $this->authorize($id ? 'update' : 'create', $id ? $release : Release::class);
            if ($id && $release->state === 'published' && $validated['version'] !== $release->version) {
                throw ValidationException::withMessages(['version' => 'A versão de uma nota publicada não pode ser alterada.']);
            }
            foreach ($validated as $field => $value) {
                $release->{$field} = $value;
            }
            if (!$id) {
                $release->state = 'draft';
                $release->created_by = auth()->id();
            }
            $release->updated_by = auth()->id();
            $release->save();
            app(RecordReleaseRevisionAction::class)->execute($release, $id ? 'edited' : 'created');

            return $release;
        });
    }
}
