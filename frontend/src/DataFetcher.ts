import { stateManager } from './StateManager'

export default function updateCategories() {
    let products = getProducts().then((categories: Category[]) => {
      stateManager.updateState(categories);
    });
}

async function getProducts(): Promise<Category[]> {
    const res = await fetch(
        `${process.env.BACKEND}/api/get-scraping-result`,
        {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
          },
          credentials: 'include',
        }
      )

  const data = await res.json();
  return data as Category[];
}