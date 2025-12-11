@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            successAlert("{{ session('success') }}");
        });
    </script>
@endif

@if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            errorAlert("{{ session('error') }}");
        });
    </script>
@endif

@if (session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            infoAlert("{{ session('info') }}");
        });
    </script>
@endif