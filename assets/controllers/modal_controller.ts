import { Controller } from '@hotwired/stimulus';
import { Modal } from 'bootstrap';
import { useDispatch } from 'stimulus-use';
import axios from "axios";
import AbstractController from "../AbstractController";

export default class extends AbstractController<HTMLElement> {
  static values = {
    contentUrl: String,
    title: String,
  };

  declare readonly contentUrlValue: string;
  declare readonly titleValue: string;
  modal = Modal;

  connect() {
    useDispatch(this);
  }

  async openModal(event) {
    const element = document.getElementById('modalDialog');
    if(null !== element) {
        this.modal = new Modal<any>(element);
        const request = axios.get(this.contentUrlValue);

        request.then(result => {
            const titles = element.getElementsByClassName('modal-title');
            titles[0].innerHTML = this.titleValue;
            const body = element.getElementsByClassName('modal-body');
            body[0].innerHTML = result.data;
            if(null !== this.modal) {
                this.modal.show();
            }
        })
    }
  }
}
