import { test, expect } from "@playwright/test";
import { login } from "./helpers/auth";

const privatePages = [
  { path: "/administrador", key: "dashboard" },
  { path: "/administrador/productos", key: "productos" },
  { path: "/administrador/clientes", key: "clientes" },
  { path: "/administrador/facturas", key: "facturas" },
  { path: "/administrador/vender", key: "vender" },
];

test.describe("Visual parity (pantalla a pantalla)", () => {
  test("login page matches baseline", async ({ page }) => {
    await page.goto("/login");
    await expect(page).toHaveScreenshot("login-page.png", { fullPage: true });
  });

  for (const pageData of privatePages) {
    test(`${pageData.key} matches baseline`, async ({ page }) => {
      await login(page);
      await page.goto(pageData.path);
      await page.waitForLoadState("networkidle");
      await expect(page).toHaveScreenshot(`${pageData.key}-page.png`, { fullPage: true });
    });
  }
});

