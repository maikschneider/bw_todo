import './components/profile-list'
import './components/profile-list-item'
import './components/task-list'
import './components/task-list-item'

import {bulmaStyles} from '@granite-elements/granite-lit-bulma/granite-lit-bulma-min.js'
import {LitElement, html, css} from 'lit'
import {customElement, property} from 'lit/decorators.js'

export interface Task {
    title: string
    uid: number
    profile: number
    description: string
    dueDate: Date
}

export interface Profile {
    name: string
    uid: number
    tasks: Task[]
}

LitElement.disableWarning?.('change-in-update');

@customElement('todo-app')
export class App extends LitElement {

    static get styles() {
        return [bulmaStyles, css`
			.progress-bar {
				height: 2rem;
			}
    	`]
    }

    @property()
    selectedProfile?: Profile = null

    @property()
    isLoading: boolean = false

    @property()
    isOnload: boolean = true

    _profileSelected(event) {
        this.selectedProfile = event.detail
        localStorage.setItem('selectedProfile', event.detail.uid)
    }

    _loadingProgressChanged(event) {
        this.isLoading = event.detail
    }

    connectedCallback() {
        super.connectedCallback();
        window.addEventListener('profile-item-selected', this._profileSelected.bind(this))
        window.addEventListener('new-loading-progress', this._loadingProgressChanged.bind(this))
        window.addEventListener('profile-switch', this.switchProfile.bind(this))
        window.addEventListener('profile-onload-request-done', () => {
            this.isOnload = false
        });
    }

    switchProfile() {
        this.selectedProfile = null
        localStorage.removeItem('selectedProfile')
    }

    render() {

        let currentView = html`
            <profile-list ?hidden="${this.isOnload}"></profile-list>`

        if (this.selectedProfile) {
            currentView = html`
                <task-list .selectedProfile="${this.selectedProfile}"></task-list>`
        }

        return html`

            <nav class="navbar" role="navigation" aria-label="main navigation">
                <div class="navbar-brand">
                    <a class="navbar-item" href="https://bulma.io">
                        <img src="https://bulma.io/images/bulma-logo.png" width="112" height="28">
                    </a>
                </div>
            </nav>

            <div class="container is-max-desktop main px-2 my-6">

                <div class="progress-bar">
                    ${!this.isLoading ? null : html`
                        <progress class="progress is-small is-primary" max="100">15%</progress>`}
                </div>

                ${currentView}

            </div>`
    }
}
