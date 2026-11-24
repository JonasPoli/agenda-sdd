import { Controller } from '@hotwired/stimulus';

/*
 * Um único <wa-dialog> por página de lista. O botão Excluir de cada linha passa a URL da ação,
 * o token CSRF e o título; o controller preenche o formulário do diálogo e o abre.
 * Confirmar envia o POST; cancelar só fecha.
 */
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['dialogo', 'formulario', 'token', 'titulo'];

    abrir(event) {
        const { url, token, titulo } = event.params;

        this.formularioTarget.action = url;
        this.tokenTarget.value = token;
        this.tituloTarget.textContent = titulo;
        this.dialogoTarget.open = true;
    }

    cancelar() {
        this.dialogoTarget.open = false;
    }

    confirmar() {
        this.formularioTarget.requestSubmit();
    }
}
