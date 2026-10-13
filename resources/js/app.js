import './bootstrap';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('dialogPanel', (wire, model, local = false) => ({
        open: local ? false : wire.entangle(model).live,
        trigger: null,
        init() {
            this.$watch('open', (value) => {
                if (value && !this.$refs.dialog.open) {
                    this.trigger = document.activeElement;
                    this.$refs.dialog.showModal();
                } else if (!value && this.$refs.dialog.open) {
                    this.$refs.dialog.close();
                    if (this.trigger?.isConnected) {
                        this.trigger.focus();
                    } else {
                        const heading = document.querySelector('main h1');
                        if (heading) {
                            heading.tabIndex = -1;
                            heading.focus({ preventScroll: true });
                        }
                    }
                }
            });
        },
    }));
});

document.addEventListener('livewire:init', () => {
    window.Livewire.interceptRequest(({ onError, onFailure, onSuccess }) => {
        onError(({ response, preventDefault }) => {
            preventDefault();
            const expired = response.status === 419 || (response.redirected && response.url.includes('/login'));
            const message = expired
                ? 'Sua sessão expirou. Entre novamente antes de continuar. Os dados permanecem nesta tela.'
                : response.status === 403
                    ? 'Você não tem permissão para concluir esta ação. Atualize a página para verificar seu acesso.'
                    : 'Não foi possível concluir a ação. Seus dados foram mantidos; tente novamente.';
            window.dispatchEvent(new CustomEvent('request-fault', { detail: { message, expired } }));
        });
        onFailure(() => window.dispatchEvent(new CustomEvent('request-fault', {
            detail: { message: 'Sem resposta do servidor. Confira a conexão e tente novamente.', expired: false },
        })));
        onSuccess(({ json }) => {
            if (json.components?.some(component => component.effects?.redirect)) {
                window.dispatchEvent(new CustomEvent('server-navigation'));
            }
            window.dispatchEvent(new CustomEvent('request-recovered'));
        });
    });
});
