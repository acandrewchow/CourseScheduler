// swagger-ui.js
window.onload = function () {
  const ui = SwaggerUIBundle({
    url: "../api.yml",
    dom_id: "#swagger-ui",
    deepLinking: true,
    presets: [
      SwaggerUIBundle.presets.apis,
      SwaggerUIBundle.SwaggerUIStandalonePreset,
    ],
    plugins: [SwaggerUIBundle.plugins.DownloadUrl],
  });
  window.ui = ui;
};
