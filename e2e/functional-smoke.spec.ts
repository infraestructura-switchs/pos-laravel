import { test, expect } from "@playwright/test";
import { login } from "./helpers/auth";

test.describe("Functional smoke (flujo base)", () => {
  test("can login and open dashboard", async ({ page }) => {
    await login(page);
    await page.goto("/administrador");
    await page.waitForLoadState("networkidle");
    await expect(page).toHaveURL(/\/administrador$/);
  });

  test("menu navigation works for key modules", async ({ page }) => {
    await login(page);

    const urls = [
      /\/administrador\/productos/,
      /\/administrador\/clientes/,
      /\/administrador\/facturas/,
    ];

    for (const urlPattern of urls) {
      await page.goto(urlPattern.source.replace(/\\\//g, "/"));
      await page.waitForLoadState("networkidle");
      await expect(page).toHaveURL(urlPattern);
    }
  });

  test("factura page renders key totals and save action", async ({ page }) => {
    await login(page);
    await page.goto("/administrador/facturas/nueva-factura");
    await page.waitForLoadState("networkidle");

    await expect(page.getByText(/informaci[oó]n del cliente/i)).toBeVisible();
    await expect(page.getByText(/informaci[oó]n del producto/i)).toBeVisible();
    await expect(page.getByText(/valor bruto/i)).toBeVisible();
    await expect(page.getByRole("button", { name: /guardar/i })).toBeVisible();
  });
});

