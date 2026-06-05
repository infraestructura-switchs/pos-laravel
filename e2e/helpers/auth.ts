import { expect, type Page } from "@playwright/test";

const LOGIN_PATH = "/login";

export async function login(page: Page) {
  const email = process.env.E2E_USER_EMAIL || "superadmin@gmail.com";
  const password = process.env.E2E_USER_PASSWORD || "12345678";

  await page.goto(LOGIN_PATH);

  // Si ya está autenticado y redirigido, no repetir login.
  if (!page.url().includes("/login")) {
    return;
  }

  await page.getByRole("textbox", { name: /email/i }).fill(email);
  await page.getByRole("textbox", { name: /contrase|password/i }).fill(password);

  const loginButton = page.getByRole("button", { name: /iniciar|inicio|login/i }).first();
  await loginButton.click();

  await page.waitForLoadState("networkidle");
  await expect(page).not.toHaveURL(/\/login$/);
}

