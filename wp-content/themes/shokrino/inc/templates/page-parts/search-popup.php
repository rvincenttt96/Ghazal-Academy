<?php
defined( 'ABSPATH' ) || exit;
?>
<div class="shkrn-header-search-box flex-center shkrn-only-desktop" onclick="close_searchbox_shkrn()">
    <form id="shkrn-popup-form-search" action="<?php echo get_site_url(); ?>/">
        <label>
            <input class="shkrn-popup-form-search-input" type="text" placeholder="متن را برای جستجو وارد کنید..." name="s" value="<?php the_search_query(); ?>" autofocus />
        </label>
        <button type="submit">
            <svg id="search" xmlns="http://www.w3.org/2000/svg" height="35px" viewBox="0 0 28.762 28.762">
                <path id="Path_7" data-name="Path 7" d="M0,0H28.762V28.762H0Z" fill="none"/>
                <circle id="Ellipse_1" data-name="Ellipse 1" cx="8" cy="8" r="8" transform="translate(4 4)" fill="none" stroke="#7d7c7c" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                <line id="Line_1" data-name="Line 1" x1="7" y1="7" transform="translate(18 18)" fill="none" stroke="#7d7c7c" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
            </svg>
        </button>
    </form>
</div>