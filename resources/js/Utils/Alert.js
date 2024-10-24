
import { ref } from 'vue';

export function reset() {
    const successMessage = ref('');
    const errorMessage = ref('');

    const resetMessages = () => {
        setTimeout(() => {
            successMessage.value = '';
            errorMessage.value = '';
        }, 5000); // Reset after 5 seconds
    };

    return {
        successMessage,
        errorMessage,
        resetMessages
    };
}
