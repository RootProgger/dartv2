import {
    FormSubmission,
    FormSubmissionDelegate,
    FormSubmissionResult
} from "@hotwired/turbo/dist/types/core/drive/form_submission";

const TurboHelper = class {
  constructor() {
    document.addEventListener('turbo:before-cache', () => {
      this.closeModal();
      this.showModal();
      this.reenableSubmitButtons();
    });

    this.initializeTransitions();
    document.addEventListener('turbo:submit-start', (event: CustomEvent) => {
      const submitter = event.detail.formSubmission.submitter;
      submitter.toggleAttribute('disabled', true);
      submitter.classList.add('turbo-submit-disabled');
    })

  }

  reenableSubmitButtons() {
    document.querySelectorAll('.turbo-submit-disabled').forEach((button) => {
      button.toggleAttribute('disabled', false);
      button.classList.remove('turbo-submit-disabled');
    });
  }

  initializeTransitions() {
    document.addEventListener('turbo:visit', () => {
      document.body.classList.add('turbo-loading');
    });
    document.addEventListener('turbo:before-render', (event: CustomEvent) => {
      if(this.isPreviewRendered())
      {
        // this is a preview that has been instantly swapped
        // remove .turbo-loading so the preview starts fully opaque
        event.detail.newBody.classList.add('turbo-loading');
        requestAnimationFrame(() => {
          document.body.classList.add('turbo-loading');
        });
      } else {
        const isRestoration = event.detail.newBody.classList.contains('turbo-loading');
        // when we are *about* to render a fresh page
        // we should already be faded out, so start us faded out
        event.detail.newBody.classList.add('turbo-loading');
      }

    });

    document.addEventListener('turbo:render', () => {
      if(!this.isPreviewRendered())
      {
        // if this is a preview, then we do nothing: stay faded out
        // after rendering the REAL page, we first allow the .turbo-loading to
        // instantly start the page at lower opacity. THEN remove the class
        // one frame later, which allows the fade in
        requestAnimationFrame(() => {
          document.body.classList.remove('turbo-loading');
        });
      }
    });
  }

  isPreviewRendered() {
    return document.documentElement.hasAttribute('data-turbo-preview');
  }

  findCacheControlMeta = () => {
    return document.querySelector('meta[name="turbo-cache-control"]') as HTMLElement;
  }

  closeModal() {
    document.addEventListener('hidden.bs.modal', () => {
      const meta = this.findCacheControlMeta();
      // only remove it if we added it
      if (!meta || !meta.dataset.removable) {
        return;
      }

      meta.remove();
    });
  }

  showModal() {
    document.addEventListener('show.bs.modal', () => {
      if(this.findCacheControlMeta())
      {
        // don't modify an existing one
        return;
      }

      const meta = document.createElement('meta');
      meta.name = 'turbo-cache-control';
      meta.content = 'no-cache';
      meta.dataset.removable = 'true';
      const headElement: HTMLHeadElement | null = document.querySelector('head');
      if(null !== headElement) {
          headElement.appendChild(meta);
      }
    });
  }
}

export default new TurboHelper();
