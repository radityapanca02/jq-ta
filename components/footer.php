        </main>
    </div>
</div>
<div class="overlay"></div>
<script>
document.getElementById('menu-toggle').addEventListener('click', function(){
    document.querySelector('.sidebar').classList.toggle('active');
    document.querySelector('.overlay').classList.toggle('active');
});
document.querySelector('.overlay').addEventListener('click', function(){
    document.querySelector('.sidebar').classList.remove('active');
    this.classList.remove('active');
});
</script>
</body>
</html>
