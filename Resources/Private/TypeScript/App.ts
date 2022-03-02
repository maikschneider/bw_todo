import './components/profile-list'
import './components/profile-list-item'
import './components/task-list'
import './components/task-list-item'
import '../Scss/App.scss'

import { bulmaStyles } from '@granite-elements/granite-lit-bulma/granite-lit-bulma-min.js'
import { LitElement, html, css } from 'lit'
import { customElement, property } from 'lit/decorators.js'

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

LitElement.disableWarning?.('change-in-update')

@customElement('todo-app')
export class App extends LitElement {
  static get styles () {
    return [bulmaStyles, css`
        .progress-bar {
            height: 2rem;
            }`
    ]
  }

    @property()
    selectedProfile?: Profile = null

    @property()
    isLoading: boolean = false

    @property()
    isOnload: boolean = true

    _profileSelected (event) {
      this.selectedProfile = event.detail
      localStorage.setItem('selectedProfile', event.detail.uid)
    }

    _loadingProgressChanged (event) {
      this.isLoading = event.detail
    }

    connectedCallback () {
      super.connectedCallback()
      window.addEventListener('profile-item-selected', this._profileSelected.bind(this))
      window.addEventListener('new-loading-progress', this._loadingProgressChanged.bind(this))
      window.addEventListener('profile-switch', this.switchProfile.bind(this))
      window.addEventListener('profile-onload-request-done', () => {
        this.isOnload = false
      })
    }

    switchProfile () {
      this.selectedProfile = null
      localStorage.removeItem('selectedProfile')
    }

    render () {
      let currentView = html`
            <profile-list ?hidden="${this.isOnload}"></profile-list>`

      if (this.selectedProfile) {
        currentView = html`
                <task-list .selectedProfile="${this.selectedProfile}"></task-list>`
      }

      return html`

            <nav class="navbar" role="navigation" aria-label="main navigation">
                <div class="navbar-brand">
                    <a class="navbar-item" href="/">
                        <svg width="120" viewBox="0 0 232 74" xmlns="http://www.w3.org/2000/svg">
                            <g fill-rule="nonzero" fill="none">
                                <path d="M28.531 19.212a2.224 2.224 0 0 1 2.224-2.223h26.43a2.224 2.224 0 0 1 0 4.447h-26.43a2.223 2.223 0 0 1-2.224-2.224Zm-8.633-6.042-5.573 7.07-1.726-2.279a2.23 2.23 0 0 0-3.558 2.685l3.472 4.57a2.222 2.222 0 0 0 1.754.889h.022a2.22 2.22 0 0 0 1.745-.848l7.355-9.338a2.22 2.22 0 0 0-.367-3.122 2.224 2.224 0 0 0-3.124.367v.006Zm37.286 21.607h-26.43a2.224 2.224 0 0 0 0 4.447h26.43a2.224 2.224 0 0 0 0-4.447Zm0 17.789h-26.43a2.224 2.224 0 0 0 0 4.447h26.43a2.224 2.224 0 1 0 0-4.447ZM23.124 32.33v9.338a2.223 2.223 0 0 1-2.223 2.224h-9.339a2.223 2.223 0 0 1-2.223-2.224v-9.338a2.223 2.223 0 0 1 2.223-2.224h9.34a2.223 2.223 0 0 1 2.223 2.222v.002Zm-4.446 2.223h-4.892v4.892h4.892v-4.892Zm4.447 15.565v9.339A2.223 2.223 0 0 1 20.9 61.68h-9.339a2.223 2.223 0 0 1-2.223-2.223v-9.339a2.223 2.223 0 0 1 2.223-2.224h9.34a2.223 2.223 0 0 1 2.223 2.223v.001Zm-4.447 2.224h-4.892v4.892h4.892v-4.893Z"
                                      fill="#00D1B2"/>
                                <g fill="#000">
                                    <path d="M99.118 24.088v28.208c0 1.16.903 1.763 2.064 1.763s2.064-.602 2.064-1.763V24.088h9.761c.99 0 1.634-.86 1.634-1.85 0-1.031-.602-1.934-1.634-1.934H89.314c-1.032 0-1.634.903-1.634 1.935 0 .989.602 1.849 1.677 1.849h9.761ZM130.422 19.659c-9.245 0-15.05 6.837-15.05 17.372 0 10.664 5.848 17.458 15.05 17.458 9.202 0 14.964-6.837 14.964-17.415 0-10.621-5.762-17.415-14.964-17.415Zm0 3.698c6.536 0 10.707 5.332 10.707 13.459 0 8.643-3.999 13.932-10.707 13.932-6.579 0-10.793-5.375-10.793-13.674 0-8.3 4.128-13.717 10.793-13.717ZM151.406 49.2c0 3.268 1.333 4.6 4.386 4.6h7.654c9.417 0 15.093-6.406 15.093-17.113 0-10.363-5.59-16.383-15.093-16.383h-7.654c-3.053 0-4.386 1.333-4.386 4.644V49.2Zm4.085-23.78c0-.902.602-1.418 1.42-1.418h6.45c7.094 0 10.921 4.515 10.921 12.728 0 8.385-4.085 13.33-11.05 13.33h-6.365c-.86 0-1.376-.473-1.376-1.462V25.42ZM198.19 19.659c-9.245 0-15.05 6.837-15.05 17.372 0 10.664 5.848 17.458 15.05 17.458 9.202 0 14.964-6.837 14.964-17.415 0-10.621-5.762-17.415-14.964-17.415Zm0 3.698c6.536 0 10.707 5.332 10.707 13.459 0 8.643-3.999 13.932-10.707 13.932-6.579 0-10.793-5.375-10.793-13.674 0-8.3 4.128-13.717 10.793-13.717Z"/>
                                </g>
                            </g>
                        </svg>
                    </a>
                </div>
            </nav>

            <div class="container is-max-desktop main px-2 my-6">

                <div class="progress-bar">
                    ${!this.isLoading
                        ? null
                        : html`
                            <progress class="progress is-small is-primary" max="100">15%</progress>`}
                </div>

                ${currentView}

            </div>`
    }
}
