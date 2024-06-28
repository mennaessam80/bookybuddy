<style>
.nnavbar {
    position: sticky;
    width: 100%;
    overflow: auto;
    z-index: 999;
    padding-top: 3px;
    text-align: center;

}

.nnavbar a {
    padding: 5px 20px;
    margin: 0rem 1rem;
    font-size: 2rem;
    color: var(--light-color);

}

.nnavbar a:hover {
    color: var(--blue);
}

/* #DDDAD1*/


@media screen and (max-width: 500px) {
    .nnavbar a {
        float: none;
        display: block;
        width: 100%;
        text-align: left;
    }
}
</style>
<div class="nnavbar">
    <a href="shop.php" class="aa">books</a>
    <a href="scsu.php" class="b">School Supplies</a>
    <a href="calc.php" class="c">Calculators</a>
    <a href="bg.php" class="d">bags & pencil cases</a>
</div>