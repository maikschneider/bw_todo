import { html, LitElement } from 'lit'
import { Profile } from '../App'
import { customElement, property, query } from 'lit/decorators.js'
import { bulmaStyles } from '@granite-elements/granite-lit-bulma/granite-lit-bulma-min.js'

@customElement('profile-list-item')
export class ProfileListItem extends LitElement {
  static get styles () {
    return [bulmaStyles]
  }

    @property()
    profile: Profile

    @property()
    showInput: boolean

    @query('#profileNameInput')
    profileNameInput: HTMLInputElement

    constructor () {
      super()
      this.showInput = false
    }

    _onProfileSelect () {
      window.dispatchEvent(new CustomEvent('profile-item-selected', { detail: this.profile }))
    }

    _onProfileDelete (event: Event) {
      window.dispatchEvent(new CustomEvent('new-loading-progress', { detail: true }))
      fetch(`/profile/${this.profile.uid}.json`, { method: 'DELETE' })
        .then(response => {
          window.dispatchEvent(new CustomEvent('new-loading-progress', { detail: false }))
          window.dispatchEvent(new CustomEvent('profile-item-deleted'))
          this.requestUpdate()
        }).catch(error => {
          window.dispatchEvent(new CustomEvent('loading-error', { detail: error }))
        })
    }

    async _onUpdateProfile () {
      window.dispatchEvent(new CustomEvent('new-loading-progress', { detail: true }))

      const name = this.profileNameInput.value
      const formData = new FormData()
      formData.set('name', name)

      await fetch(`/profile/${this.profile.uid}.json`, { method: 'PATCH', body: formData })
        .then(response => response.json())
        .then(data => {
          this.profile = data
          this.showInput = false
          this.requestUpdate()
          window.dispatchEvent(new CustomEvent('new-loading-progress', { detail: false }))
        })
        .catch(error => {
          window.dispatchEvent(new CustomEvent('loading-error', { detail: error }))
        })
    }

    _onNameClick () {
      this.showInput = true
      // this.profileNameInput.focus();
    }

    render () {
      let elementContent = html`<p class="subtitle is-4" @click="${this._onNameClick}">${this.profile.name}</p>`

      if (this.showInput) {
        elementContent = html`
                <div class="field has-addons">
                    <div class="control is-expanded">
                        <input id="profileNameInput"
                               value="${this.profile.name}"
                               class="input is-medium"
                               type="text"
                               placeholder="Profile name">
                    </div>
                    <div class="control">
                        <a class="button is-medium" @click="${this._onUpdateProfile}">
                            save
                        </a>
                    </div>
                </div>`
      }

      return html`
            <div class="box mb-4">
                <div class="columns is-align-items-center">
                    <div class="column">
                        ${elementContent}
                    </div>
                    <div class="column is-narrow">
                        <button @click="${this._onProfileSelect}" class="button is-primary is-rounded is-outlined">
                            Select
                        </button>
                        <button @click="${this._onProfileDelete}" class="button is-danger is-rounded is-outlined">Delete
                        </button>
                    </div>
                </div>
            </div>
        `
    }
}
