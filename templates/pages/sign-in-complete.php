<section class="panel narrow">
  <h1>Completing sign-in</h1>
  <p class="status" data-complete-status role="status" aria-live="polite">Checking your link...</p>
  <script>
    window.__YATSN_SIGNIN_TOKEN__ = <?= json_encode($token ?? '', JSON_THROW_ON_ERROR) ?>;
  </script>
</section>
