import { Controller } from "@hotwired/stimulus";

export default abstract class AbstractController<StimulusElement extends Element> extends Controller {
    // @ts-ignore
    declare readonly element: StimulusElement;
}
