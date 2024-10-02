import renderChart from "./chartLoader";
import updateCategories from "./DataFetcher";
import { stateManager } from "./StateManager";



stateManager.subscribe(() => {
    const state = stateManager.getState();
    renderChart(state);
});
updateCategories();
