<div class="footer">
    <div class="copyright">
        <p> Copyright  &copy; <span id="year"></span> {{config('app.name')}}, All Rights Reserved. Developed by
            <a href=" {{config('app.author_url')}}" target="_blank"> {{config('app.author')}}</a></p>


    </div>
</div>


<script>
    let year = document.getElementById('year');
    let currentYear = new Date().getFullYear();
    year.innerHTML = currentYear;
</script>
