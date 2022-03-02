import { html, LitElement } from 'lit'
import { Profile } from '../App'
import { customElement, property, query } from 'lit/decorators.js'
import { bulmaStyles } from '@granite-elements/granite-lit-bulma/granite-lit-bulma-min.js'

@customElement('profile-list')
export class ProfileList extends LitElement {
  static get styles () {
    return [bulmaStyles]
  }

    @property()
    profiles: Profile[] = [];

    @query('#profileFormInput')
    profileFormInput: HTMLInputElement;

    async loadProfiles () {
      window.dispatchEvent(new CustomEvent('new-loading-progress', { detail: true }))
      await fetch('/profile.json')
        .then(response => response.json())
        .then(data => {
          this.profiles = data
          window.dispatchEvent(new CustomEvent('new-loading-progress', { detail: false }))
        })
        .catch((error) => {
          console.error('Error:', error)
        })
    }

    loadSelectedProfile () {
      const selectedProfile = localStorage.getItem('selectedProfile')
      if (!selectedProfile) {
        return
      }
      const profile = this.profiles.find(profile => profile.uid === parseInt(selectedProfile))
      if (profile) {
        window.dispatchEvent(new CustomEvent('profile-item-selected', { detail: profile }))
      }
    }

    connectedCallback () {
      super.connectedCallback()
      this.loadProfiles()
        .then(() => {
          this.loadSelectedProfile()
          window.dispatchEvent(new Event('profile-onload-request-done'))
        })
      window.addEventListener('profile-item-deleted', this.loadProfiles.bind(this))
    }

    disconnectedCallback () {
      super.disconnectedCallback()
      window.removeEventListener('profile-item-deleted', this.loadProfiles.bind(this))
    }

    addProfile () {
      const profileName = this.profileFormInput.value
      if (!profileName) {
        return
      }
      window.dispatchEvent(new CustomEvent('new-loading-progress', { detail: true }))

      const formData = new FormData()
      formData.append('name', profileName)

      fetch('/profile.json', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          this.loadProfiles().then(() => {
            window.dispatchEvent(new CustomEvent('profile-item-selected', { detail: data }))
          })
          this.profileFormInput.value = ''
        })
        .catch((error) => {
          console.error('Error:', error)
        })
    }

    render () {
      return html`
            <h1 class="title is-1">Profiles</h1>            <h2 class="subtitle">Select or edit your
                profiles</h2>                ${this.profiles.map((profile) =>
                html`
                    <profile-list-item .profile="${profile}"></profile-list-item>
                `
            )}

            <div class="notification mb-4" ?hidden="${this.profiles.length}">
                <p>No profiles found</p>
            </div>

            <div class="box">
                <div class="field has-addons">
                    <div class="control is-expanded">
                        <input id="profileFormInput" class="input is-medium" type="text" placeholder="Profile name">
                    </div>
                    <div class="control">
                        <a class="button is-primary is-medium" @click="${this.addProfile}">
                            + Create profile
                        </a>
                    </div>
                </div>
            </div>`
    }
}
