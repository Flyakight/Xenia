/**
 * Tannic Studio — Contact Scripts
 *
 * Handles: Budget input masking (auto-commas, $ prefix).
 *
 * Only loaded on the Reservation Desk page template.
 *
 * @package TannicStudio
 */

(function () {
  'use strict';

  /* ====================================================================
     BUDGET INPUT MASKING
     ==================================================================== */

  function initBudgetMask() {
    var input = document.getElementById('budget-input');
    if (!input) return;

    var formatter = new Intl.NumberFormat('en-US');

    input.addEventListener('input', function () {
      // Strip everything except digits
      var raw = input.value.replace(/[^0-9]/g, '');

      if (raw === '') {
        input.value = '';
        return;
      }

      var num = parseInt(raw, 10);
      input.value = '$' + formatter.format(num);
    });

    // Only allow numeric input + navigation keys
    input.addEventListener('keydown', function (e) {
      var allowed = [
        'Backspace',
        'Delete',
        'ArrowLeft',
        'ArrowRight',
        'Tab',
        'Home',
        'End',
      ];

      if (allowed.indexOf(e.key) !== -1) return;
      if (e.ctrlKey || e.metaKey) return; // Allow copy/paste/select-all

      if (!/\d/.test(e.key)) {
        e.preventDefault();
      }
    });

    // Set initial state
    input.setAttribute('inputmode', 'numeric');
    input.setAttribute('autocomplete', 'off');
  }

  /* ====================================================================
     INIT
     ==================================================================== */

  document.addEventListener('DOMContentLoaded', initBudgetMask);
})();
