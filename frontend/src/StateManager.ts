"use strict";

type StateListener = () => void;

class StateManager {
    private state: { categories: Category[] } = { categories: [] };
    private listeners: StateListener[] = [];

    getState() {
        return this.state;
    }

    updateState(newCategories: Category[]) {
        this.state.categories = newCategories;
        this.notifyListeners();
    }

    subscribe(listener: StateListener) {
        this.listeners.push(listener);
    }

    private notifyListeners() {
        this.listeners.forEach(listener => listener());
    }
}

export const stateManager = new StateManager();