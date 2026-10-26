(function () {
  if (!window.wc || !wc.wcBlocksRegistry || !wp.element) return;
  var settings = wc.wcSettings.getSetting("fiura_data", {});
  var el = wp.element.createElement;
  wc.wcBlocksRegistry.registerPaymentMethod({
    name: "fiura",
    label: el("span", null, settings.title || "Fiura Pay"),
    ariaLabel: settings.title || "Fiura Pay",
    content: el("div", null, settings.description || ""),
    edit: el("div", null, settings.description || ""),
    canMakePayment: function () {
      return true;
    },
    supports: { features: settings.supports || ["products"] },
  });
})();
