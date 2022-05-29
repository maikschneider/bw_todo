import { html, LitElement } from 'lit'
import { Profile } from '../App'
import { customElement, property, query } from 'lit/decorators.js'
import { bulmaStyles } from '@granite-elements/granite-lit-bulma/granite-lit-bulma-min.js'
import moment from 'moment-mini'

@customElement('task-list')
export class TaskList extends LitElement {
  static get styles () {
    return [bulmaStyles]
  }

    @property()
    selectedProfile?: Profile = null;

    @query('#taskTitleInput')
    taskTitleInput: HTMLInputElement;

    @query('#taskDescriptionInput')
    taskDescriptionInput: HTMLInputElement;

    @query('#taskDueDateInput')
    taskDueDateInput: HTMLInputElement;

    connectedCallback () {
      super.connectedCallback()
      window.addEventListener('task-item-deleted', this._taskDeleted.bind(this))
    }

    _taskDeleted (event) {
      this.selectedProfile.tasks = this.selectedProfile.tasks.filter(task => event.detail !== task)
      this.requestUpdate()
    }

    addTask () {
      const title = this.taskTitleInput.value
      if (!title) {
        return
      }
      window.dispatchEvent(new CustomEvent('new-loading-progress', { detail: true }))

      // build post data
      const formData = new FormData()
      formData.append('title', title)
      formData.append('description', this.taskDescriptionInput.value)
      if (this.taskDueDateInput.value) {
        const date = moment(this.taskDueDateInput.value).format('DD.MM.YYYY-HH:mm')
        formData.append('dueDate', date)
      }

      fetch('/profile/' + this.selectedProfile.uid + '/task.json', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          window.dispatchEvent(new CustomEvent('task-item-created', { detail: true }))
          this.selectedProfile.tasks.push(data)
          this.taskTitleInput.value = ''
          this.taskDescriptionInput.value = ''
          this.taskDueDateInput.value = ''
          this.requestUpdate()
          window.dispatchEvent(new CustomEvent('new-loading-progress', { detail: false }))
        })
        .catch((error) => {
          window.dispatchEvent(new CustomEvent('loading-error', { detail: error }))
        })
    }

    render () {
      return html`
            <div class="columns is-align-items-center is-medium">
                <div class="column">
                    <h1 class="title is-1">Tasks</h1>
                    <h2 class="subtitle">Add tasks to profile
                        <strong>${this.selectedProfile ? this.selectedProfile.name : null}</strong></h2>
                </div>
                <div class="column is-narrow">
                    <a class="button is-primary is-rounded"
                       @click="${() => window.dispatchEvent(new Event('profile-switch'))}">Switch profile
                    </a>
                </div>
            </div>

            ${this.selectedProfile.tasks.map((task) =>
                html`
                    <task-list-item .task="${task}"></task-list-item>
                `
            )}

            <div class="notification mb-4" ?hidden="${this.selectedProfile.tasks.length}">
                <p>No task found</p>
            </div>

            <hr class="my-6"/>

            <div class="box">
                <h5 class="title is-5">Create new Task</h5>
                <div class="field">
                    <div class="control is-expanded">
                        <input id="taskTitleInput" class="input is-medium" type="text" placeholder="Title">
                    </div>
                </div>
                <div class="field">
                    <div class="control is-expanded">
                        <input type="datetime-local" id="taskDueDateInput" class="input is-medium" placeholder="11"/>
                    </div>
                </div>
                <div class="field">
                    <div class="control is-expanded">
                        <textarea id="taskDescriptionInput" class="textarea" placeholder="Description"></textarea>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <a class="button is-primary is-medium" @click="${this.addTask}">
                            + Create task
                        </a>
                    </div>
                </div>
            </div>
        `
    }
}
