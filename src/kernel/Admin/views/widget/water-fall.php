<style>
    /**
 * Grid container
 */
    .tiles {
        list-style-type: none;
        position: relative; /** Needed to ensure items are laid out relative to this container **/
        margin: 0;
        padding: 0;
    }

    /**
     * Grid items
     */
    .tiles li {
        width: 160px;    float: left;
        background-color: #ffffff;
        border: 1px solid #dedede;
        border-radius: 2px;
        -moz-border-radius: 2px;
        -webkit-border-radius: 2px;
        display: none; /** Hide items initially to avoid a flicker effect **/
        cursor: pointer;
        padding: 4px;
    }

    .tiles li.inactive {
        visibility: hidden;
        opacity: 0;
    }

    .tiles li img {
        display: block;
    }

    /**
     * Grid item text
     */
    .tiles li p {
        color: #666;
        font-size: 12px;
        margin: 7px 0 0 7px;
    }

    .wtf-wrapper .tiles li {
        display: block;
        cursor: pointer;
        position: absolute;
        margin: 0;
        -webkit-transition: top 0.5s ease, left 0.5s ease;
        -moz-transition: top 0.5s ease, left 0.5s ease;
        -o-transition: top 0.5s ease, left 0.5s ease;
        -ms-transition: top 0.5s ease, left 0.5s ease;
    }
    .tiles img {
        display: block;
        height: auto;width:100%;
    }
    .wookmark-placeholder {
        -webkit-transition: all 0.3s ease-out;
        -moz-transition: all 0.3s ease-out;
        -o-transition: all 0.3s ease-out;
        transition: all 0.3s ease-out;
    }
    /**
     * Filters
     */
    #filters {
        list-style-type: none;
        text-align: center;
        margin: 0 5% 0 5%;
    }

    #filters li {
        font-size: 12px;
        float: left;
        padding: 6px 0 4px 0;
        cursor: pointer;
        margin: 0 1% 0 1%;
        width: 8%;
        -webkit-transition: all 0.15s ease-out;
        -moz-transition: all 0.15s ease-out;
        -o-transition: all 0.15s ease-out;
        transition: all 0.15s ease-out;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
    }

    #filters li:hover {
        background: #dedede;
    }

    #filters li.active {
        background: #333333;
        color: #ffffff;
    }
</style>
<div class="wtf-wrapper">
    <br/>
    <ol id="filters">
        <li data-filter="all">All</li>
        <li data-filter="amsterdam">Amsterdam</li>
        <li data-filter="tokyo">Tokyo</li>
        <li data-filter="london">London</li>
        <li data-filter="paris">Paris</li>
        <li data-filter="berlin">Berlin</li>
        <li data-filter="sport">Sport</li>
        <li data-filter="fashion">Fashion</li>
        <li data-filter="video">Video</li>
        <li data-filter="art">Art</li>
    </ol>
    <br/><br/><br/>
    <ul class="tiles">
        <li data-filter-class='["all", "london", "art"]'>
            <img src="/test/image_1.jpg"  >
            <p>London Art</p>
        </li>
        <li data-filter-class='["all", "berlin", "art"]'>
            <img src="/test/image_2.jpg"  >
            <p>Berlin Art</p>
        </li>
        <li data-filter-class='["all", "berlin", "video"]'>
            <img src="/test/image_3.jpg"  >
            <p>Berlin Video</p>
        </li>
        <li data-filter-class='["all", "tokyo", "fashion"]'>
            <img src="/test/image_4.jpg"  >
            <p>Tokyo Fashion</p>
        </li>
        <li data-filter-class='["all", "berlin", "art"]'>
            <img src="/test/image_5.jpg"  >
            <p>Berlin Art</p>
        </li>
        <li data-filter-class='["all", "tokyo", "fashion"]'>
            <img src="/test/image_6.jpg"  >
            <p>Tokyo Fashion</p>
        </li>
        <li data-filter-class='["all", "london", "art"]'>
            <img src="/test/image_7.jpg"  >
            <p>London Art</p>
        </li>
        <li data-filter-class='["all", "tokyo", "video"]'>
            <img src="/test/image_8.jpg" >
            <p>Tokyo Video</p>
        </li>
        <li data-filter-class='["all", "tokyo", "art"]'>
            <img src="/test/image_9.jpg"  >
            <p>Tokyo Art</p>
        </li>
        <li data-filter-class='["all", "berlin", "fashion"]'>
            <img src="/test/image_10.jpg"  >
            <p>Berlin Fashion</p>
        </li>
        <li data-filter-class='["all", "amsterdam", "art"]'>
            <img src="/test/image_1.jpg"  >
            <p>Amsterdam Art</p>
        </li>
        <li data-filter-class='["all", "paris", "video"]'>
            <img src="/test/image_2.jpg"  >
            <p>Paris Video</p>
        </li>
        <li data-filter-class='["all", "london", "video"]'>
            <img src="/test/image_3.jpg"  >
            <p>London Video</p>
        </li>
        <li data-filter-class='["all", "london", "video"]'>
            <img src="/test/image_4.jpg"  >
            <p>London Video</p>
        </li>
        <li data-filter-class='["all", "amsterdam"," video"]'>
            <img src="/test/image_5.jpg"  >
            <p>Amsterdam Video</p>
        </li>
        <li data-filter-class='["all", "tokyo", "fashion"]'>
            <img src="/test/image_6.jpg"  >
            <p>Tokyo Fashion</p>
        </li>
        <li data-filter-class='["all", "tokyo", "sport"]'>
            <img src="/test/image_7.jpg"  >
            <p>Tokyo Sport</p>
        </li>
        <li data-filter-class='["all", "berlin", "video"]'>
            <img src="/test/image_8.jpg"  >
            <p>Berlin Video</p>
        </li>
        <li data-filter-class='["all", "amsterdam", "fashion"]'>
            <img src="/test/image_9.jpg"  >
            <p>Amsterdam Fashion</p>
        </li>
        <li data-filter-class='["all", "berlin", "sport"]'>
            <img src="/test/image_10.jpg"  >
            <p>Berlin Sport</p>
        </li>
        <li data-filter-class='["all", "paris", "video"]'>
            <img src="/test/image_1.jpg"  >
            <p>Paris Video</p>
        </li>
        <li data-filter-class='["all", "tokyo", "sport"]'>
            <img src="/test/image_2.jpg"  >
            <p>Tokyo Sport</p>
        </li>
        <li data-filter-class='["all", "amsterdam", "art"]'>
            <img src="/test/image_3.jpg" >
            <p>Amsterdam Art</p>
        </li>
        <li data-filter-class='["all", "berlin", "sport"]'>
            <img src="/test/image_4.jpg"  >
            <p>Berlin Sport</p>
        </li>
        <li data-filter-class='["all", "paris", "art"]'>
            <img src="/test/image_5.jpg"  >
            <p>Paris Art</p>
        </li>
        <li data-filter-class='["all", "berlin", "art"]'>
            <img src="/test/image_6.jpg"  >
            <p>Berlin Art</p>
        </li>
        <li data-filter-class='["all", "london", "art"]'>
            <img src="/test/image_7.jpg"  >
            <p>London Art</p>
        </li>
        <li data-filter-class='["all", "london", "video"]'>
            <img src="/test/image_8.jpg"  >
            <p>London Video</p>
        </li>
        <li data-filter-class='["all", "london", "video"]'>
            <img src="/test/image_9.jpg"  >
            <p>London Video</p>
        </li>
        <li data-filter-class='["all", "paris", "video"]'>
            <img src="/test/image_10.jpg"  >
            <p>Paris Video</p>
        </li>
        <!-- End of grid blocks -->
    </ul>
</div>
<script type="text/javascript">
    __then__(function (){
        var $spa = $('#<?php echo Lxh\Admin\Admin::SPAID()?>');

        // Prepare layout options.
        var options = {
            autoResize: true, // This will auto-update the layout when the browser window is resized.
            container: $spa.find('.wtf-wrapper .tiles'), // Optional, used for some extra CSS styling
            offset: 1, // Optional, the distance between grid items
            itemWidth: 160, // Optional, the width of a grid item
            fillEmptySpace: false // Optional, fill the bottom of each column with widths of flexible height
        };

        // Get a reference to your grid items.
        var handler = $spa.find('.tiles li'),
            filters = $spa.find('#filters li');
        handler.wookmark(options);
        // Call the layout function.
        setTimeout(function () {
            handler.wookmark(options);
        }, 10);

        /**
         * When a filter is clicked, toggle it's active state and refresh.
         */
        var onClickFilter = function(event) {
            var item = $(event.currentTarget),
                activeFilters = [];
            item.toggleClass('active');

            // Collect active filter strings
            filters.filter('.active').each(function() {
                activeFilters.push($(this).data('filter'));
            });

            handler.wookmarkInstance.filter(activeFilters, 'or');
        };

        // Capture filter click events.
        filters.click(onClickFilter);
    });
</script>
