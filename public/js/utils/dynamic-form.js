/**
 * DynamicForm - Reusable utility for dynamic form fields
 * Consolidates addItem, removeItem, addStep, removeStep, etc.
 */
const DynamicForm = {
    /**
     * Add a simple text input item to a container
     * @param {string} containerId - The ID of the container element
     * @param {string} inputName - The name attribute for the input (e.g., 'objectives[]')
     * @param {string} placeholder - Placeholder text for the input
     * @param {boolean} required - Whether the input is required
     */
    addItem(containerId, inputName, placeholder, required = false) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
            <input type="text" class="form-control" name="${inputName}" placeholder="${placeholder}" ${required ? 'required' : ''}>
            <button type="button" class="btn btn-outline-danger" onclick="DynamicForm.removeItem(this)">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(div);
    },

    /**
     * Remove an item from input group (keeps at least one)
     * @param {HTMLElement} button - The button that triggered the removal
     */
    removeItem(button) {
        const inputGroup = button.closest('.input-group');
        if (!inputGroup) return;

        const container = inputGroup.parentElement;
        if (container && container.querySelectorAll('.input-group').length > 1) {
            inputGroup.remove();
        }
    },

    /**
     * Add a card-based item (for steps, checklist items, task items, etc.)
     * @param {string} containerId - The ID of the container element
     * @param {string} cardClass - CSS class for the card (e.g., 'step-card', 'item-card')
     * @param {function} templateFn - Function that returns the card's inner HTML given the current index
     */
    addCard(containerId, cardClass, templateFn) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const currentCount = container.querySelectorAll(`.${cardClass}`).length;

        const card = document.createElement('div');
        card.className = `card mb-3 ${cardClass}`;
        card.innerHTML = templateFn(currentCount);
        container.appendChild(card);

        return currentCount + 1;
    },

    /**
     * Remove a card and renumber remaining cards
     * @param {HTMLElement} button - The button that triggered the removal
     * @param {string} cardClass - CSS class of the cards (e.g., 'step-card')
     * @param {string} prefix - Prefix for numbering (e.g., 'Step', 'Item')
     */
    removeCard(button, cardClass, prefix = 'Item') {
        const card = button.closest(`.${cardClass}`);
        if (!card) return;

        const allCards = document.querySelectorAll(`.${cardClass}`);
        if (allCards.length > 1) {
            card.remove();
            this.renumberCards(cardClass, prefix);
        }
    },

    /**
     * Renumber cards after removal
     * @param {string} cardClass - CSS class of the cards
     * @param {string} prefix - Prefix for numbering (e.g., 'Step', 'Item')
     * @param {string} headerSelector - Selector for the header element to update (default 'h6')
     */
    renumberCards(cardClass, prefix = 'Item', headerSelector = 'h6') {
        const cards = document.querySelectorAll(`.${cardClass}`);
        cards.forEach((card, index) => {
            const header = card.querySelector(headerSelector);
            if (header) {
                header.textContent = `${prefix} #${index + 1}`;
            }

            // Update hidden step_number inputs if they exist
            const stepNumberInput = card.querySelector('input[name*="[step_number]"]');
            if (stepNumberInput) {
                stepNumberInput.value = index + 1;
            }
        });
    }
};

// Make globally available
window.DynamicForm = DynamicForm;
