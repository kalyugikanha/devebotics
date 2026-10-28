/**
 * Shows the chosen file name for styled file inputs.
 */
export default () => ({
    fileName: '',

    update(event) {
        const file = event.target.files && event.target.files[0];
        this.fileName = file ? file.name : '';
    },

    get hasFile() {
        return this.fileName !== '';
    },
});
