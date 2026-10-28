/**
 * Prevents double submission and shows a busy state on the submit button.
 */
export default () => ({
    submitting: false,

    submit(event) {
        if (this.submitting) {
            event.preventDefault();

            return;
        }

        this.submitting = true;
    },
});
