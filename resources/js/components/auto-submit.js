/**
 * Submits a GET filter form when a control changes (the form still works
 * without JavaScript through its submit button).
 */
export default () => ({
    submit() {
        const form = this.$el.closest('form');

        if (form) {
            form.requestSubmit();
        }
    },
});
