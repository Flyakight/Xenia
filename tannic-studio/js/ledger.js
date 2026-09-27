/**
 * Tannic Studio — Ledger Scripts
 *
 * Handles: Service accordion expand/collapse, pricing calculator,
 * manifest sidebar updates.
 *
 * Only loaded on the Service Ledger page template.
 *
 * @package TannicStudio
 */

(function () {
  'use strict';

  /* ====================================================================
     CURRENCY FORMATTER
     ==================================================================== */

  var currencyFormatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  });

  /* ====================================================================
     STATE
     ==================================================================== */

  var manifest = {
    items: new Map(),
  };

  /* ====================================================================
     1. ACCORDION EXPAND / COLLAPSE
     ==================================================================== */

  function initAccordions() {
    var buttons = document.querySelectorAll('.ledger-expand');

    buttons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var item = btn.closest('.ledger-item');
        var body = item.querySelector('.ledger-body');
        var icon = btn.querySelector('.ledger-expand-icon');
        var isOpen = btn.getAttribute('aria-expanded') === 'true';
        var glassContainer = body.querySelector('.glass-container');

        // Close all others (single-open mode)
        buttons.forEach(function (otherBtn) {
          if (otherBtn !== btn) {
            var otherItem = otherBtn.closest('.ledger-item');
            var otherBody = otherItem.querySelector('.ledger-body');
            var otherIcon = otherBtn.querySelector('.ledger-expand-icon');

            otherBtn.setAttribute('aria-expanded', 'false');
            otherBody.style.height = '0';
            otherBody.setAttribute('hidden', '');
            if (otherIcon) otherIcon.textContent = '+';
          }
        });

        // Toggle current
        if (isOpen) {
          btn.setAttribute('aria-expanded', 'false');
          body.style.height = '0';
          // Delay hiding to allow transition
          setTimeout(function () {
            body.setAttribute('hidden', '');
          }, 600);
          if (icon) icon.textContent = '+';
        } else {
          body.removeAttribute('hidden');
          btn.setAttribute('aria-expanded', 'true');
          // Calculate height from inner content
          var contentHeight = glassContainer
            ? glassContainer.offsetHeight
            : body.scrollHeight;
          body.style.height = contentHeight + 'px';
          if (icon) icon.textContent = '-';
        }
      });
    });
  }

  /* ====================================================================
     2. CALCULATOR LOGIC
     ==================================================================== */

  function initCalculator() {
    var checkboxes = document.querySelectorAll('.add-checkbox');

    checkboxes.forEach(function (cb) {
      cb.addEventListener('change', function () {
        var item = cb.closest('.ledger-item');
        var id = item.dataset.serviceId;
        var title = item.dataset.serviceTitle;
        var price = parseInt(item.dataset.servicePrice, 10) || 0;
        var weeks = parseInt(item.dataset.serviceWeeks, 10) || 0;

        if (cb.checked) {
          item.classList.add('is-selected');
          manifest.items.set(id, {
            title: title,
            price: price,
            weeks: weeks,
          });
        } else {
          item.classList.remove('is-selected');
          manifest.items.delete(id);
        }

        updateManifest();
      });
    });
  }

  /* ====================================================================
     3. MANIFEST UPDATE
     ==================================================================== */

  function updateManifest() {
    var container = document.querySelector('.manifest-items');
    var emptyMsg = container.querySelector('.manifest-empty');
    var totalWeeksEl = document.querySelector('.manifest-total-weeks');
    var totalPriceEl = document.querySelector('.manifest-total-price');

    if (!container || !totalWeeksEl || !totalPriceEl) return;

    // Remove existing manifest items (keep empty message)
    var existingItems = container.querySelectorAll('.manifest-item');
    existingItems.forEach(function (el) {
      el.remove();
    });

    var totalPrice = 0;
    var totalWeeks = 0;

    manifest.items.forEach(function (data) {
      totalPrice += data.price;
      totalWeeks += data.weeks;

      // Build DOM element safely (no innerHTML with user data)
      var el = document.createElement('div');
      el.classList.add('manifest-item');

      var titleSpan = document.createElement('span');
      titleSpan.classList.add('manifest-item-title');
      titleSpan.textContent = data.title;

      var priceSpan = document.createElement('span');
      priceSpan.classList.add('manifest-item-price', 'mono');
      priceSpan.textContent = currencyFormatter.format(data.price);

      el.appendChild(titleSpan);
      el.appendChild(priceSpan);
      container.appendChild(el);
    });

    // Toggle empty state
    if (emptyMsg) {
      emptyMsg.hidden = manifest.items.size > 0;
    }

    // Update totals
    totalWeeksEl.textContent = totalWeeks + ' Weeks';
    totalPriceEl.textContent = currencyFormatter.format(totalPrice);
  }

  /* ====================================================================
     INIT
     ==================================================================== */

  document.addEventListener('DOMContentLoaded', function () {
    initAccordions();
    initCalculator();
  });
})();
