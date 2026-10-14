<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Release;
use App\Support\AppVersion;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PublishReleaseAction
{
    use AuthorizesRequests;

    public function execute(int $id): Release
    {
        $release = Release::findOrFail($id);
        $this->authorize('publish', $release);

        return DB::transaction(function () use ($id): Release {
            $release = Release::lockForUpdate()->findOrFail($id);
            $this->authorize('publish', $release);
            if ($release->version !== AppVersion::current()) {
                throw ValidationException::withMessages(['publish' => 'Publique somente a nota correspondente à versão implantada: '.AppVersion::current().'.']);
            }
            if ($release->state === 'published') {
                return $release;
            }
            $release->state = 'published';
            $release->published_at = now();
            $release->updated_by = auth()->id();
            $release->save();
            app(RecordReleaseRevisionAction::class)->execute($release, 'published');

            return $release;
        });
    }
}
