import {html, LitElement} from "lit";
import {Task} from '../App'
import {customElement, property} from "lit/decorators.js";
import {bulmaStyles} from '@granite-elements/granite-lit-bulma/granite-lit-bulma-min.js'
import moment from 'moment-mini';
import {classMap} from 'lit/directives/class-map.js';
import {css} from "lit-element";

@customElement('task-list-item')
class TaskListItem extends LitElement {

	static get styles() {
		return [bulmaStyles, css`
			.is-in-past {
				background-color: #ff404047;
			}
		`];
	}

	@property()
	task: Task

	_onTaskDelete(event: Event) {
		window.dispatchEvent(new CustomEvent('new-loading-progress', {detail: true}));
		fetch(`/task/${this.task.uid}.json`, {method: 'DELETE'})
			.then(response => {
				window.dispatchEvent(new CustomEvent('new-loading-progress', {detail: false}));
				window.dispatchEvent(new CustomEvent('task-item-deleted', {detail: this.task}));
			}).catch(error => {
			window.dispatchEvent(new CustomEvent('loading-error', {detail: error}));
		})
	}

	formatDate(): string {
		if (!this.task.dueDate) {
			return '';
		}
		const date = new Date(this.task.dueDate)
		return (moment(this.task.dueDate)).fromNow()
	}

	render() {

		const classes = {
			box: true,
			"mb-4": true,
			"is-in-past": (moment(this.task.dueDate)).isBefore(moment.now())
		};

		return html`
			<div class="${classMap(classes)}">
				<div class="columns is-align-items-center">
					<div class="column">
						<p class="subtitle is-4 mb-1">${this.task.title}</p>
						<p>${this.task.description}</p>
					</div>
					<div class="column">
						${this.formatDate()}
					</div>
					<div class="column is-narrow">
						<button @click="${this._onTaskDelete}" class="button is-primary is-rounded is-outlined">Done
						</button>
					</div>
				</div>
			</div>
		`
	}
}
