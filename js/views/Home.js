import AbstractView from "./AbstractView.js";

export default class Home extends AbstractView {
    view_html= 'Home.html'


    constructor(params) {
        super(params);

        this.setTitle('Projeto BD')
    }


    async init(container) {

    }
}