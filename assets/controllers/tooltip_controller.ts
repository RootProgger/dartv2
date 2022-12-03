import { Controller } from '@hotwired/stimulus';
import { Tooltip } from 'bootstrap';
import AbstractController from "../AbstractController";

export default class extends AbstractController<HTMLElement> {
  static values = {
    title: String
  }

  declare readonly titleValue: string;

  connect() {
      const tooltip = new Tooltip(this.element, {
        title: this.titleValue,
        container: 'body',
        placement: 'top',
      });
  }
}
