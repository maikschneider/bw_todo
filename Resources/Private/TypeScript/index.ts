interface Task {
	title: string
	uid: number
	profile: number
	description: string
	due_date: number
}

interface Profile {
	name: string
	uid: number
	tasks: Array<Task>
}

interface ProfileList extends Array<Profile>{}

interface TaskList extends Array<Profile> {}
