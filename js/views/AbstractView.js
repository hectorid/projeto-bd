export default class AbstractView {
    view_html = ''

    constructor (params) {
        this.params = params
    }

    async init(container, data) {}

    setTitle(title) {
        document.title = title;
    }

    async getHTML() {
        try {
            return await request(`/views/${this.view_html}`, 'GET', {}, 'text')
        } catch (e) {
            console.error(`Error on the html request for the ${this.constructor.name} view`)
            return ''
        }
    }
}