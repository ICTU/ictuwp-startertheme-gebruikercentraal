!function(e,t,a){e(t).ready(function(){!function(e){e(t).ready(function(){e(".comment-reply-link").click(function(){e('input[name="comment_parent"]').val(e(this).data("commentid")),e("#reply-title").text(e(this).attr("aria-label"))})})}(jQuery),jQuery(".form-item--filter label").click(function(){var e=jQuery(this).parent();e.find("input:checked").length?e.removeClass("is-active"):e.addClass("is-active")});const n=1e3,r=t.getElementById("menu-primary"),i=t.querySelectorAll("li.main-menu__item--with-sub"),s=t.querySelectorAll(".btn--toggle-menu"),o=(t.querySelectorAll(".l-header-nav"),t.getElementById("mainnav"),t.getElementsByTagName("body"));function l(e,t){e.classList.contains("open")?(e.classList.remove("open"),e.querySelector("a").setAttribute("aria-expanded","false"),void 0!==e.querySelector("button")&&e.querySelector("button").setAttribute("aria-expanded","false"),e.querySelector("ul.main-menu__sublist").classList.add("visuallyhidden"),e.querySelector("ul.main-menu__sublist").setAttribute("aria-expanded","false")):(e.classList.add("open"),e.querySelector("a").setAttribute("aria-expanded","true"),void 0!==e.querySelector("button")&&e.querySelector("button").setAttribute("aria-expanded","true"),e.querySelector("ul.main-menu__sublist").classList.remove("visuallyhidden"),e.querySelector("ul.main-menu__sublist").setAttribute("aria-expanded","true"))}function u(){var e=a.innerWidth;t.querySelectorAll(".menu-item-has-children");Array.prototype.forEach.call(i,function(t,a){if(t.classList.remove("open"),t.querySelector("a").setAttribute("aria-expanded","false"),e>n){var r=t.querySelector("button");r&&void 0!==r&&(r.setAttribute("aria-expanded","false"),r.classList.remove("open-list")),t.querySelector("ul.main-menu__sublist").classList.add("visuallyhidden")}})}function d(e){t.querySelectorAll("button.main-menu__open-sub").forEach(function(e){e.remove()}),e<n?(Array.prototype.forEach.call(i,function(e,t){e.classList.add("open");var a=e.querySelector("ul.main-menu__sublist");a&&(a.classList.remove("visuallyhidden"),a.setAttribute("aria-expanded","true")),e.removeEventListener("pointerenter",function(e){l(this)}),e.removeEventListener("pointerleave",function(e){l(this)})}),s[0].addEventListener("click",function(e){this.classList.contains("active")?(this.classList.remove("active"),o[0].classList.remove("show-menu")):(this.classList.add("active"),o[0].classList.add("show-menu"))})):Array.prototype.forEach.call(i,function(e,t){var a=e,n=a.querySelector(".main-menu__sublist"),r=e.querySelector("a"),i='<button class="main-menu__open-sub"><span><span class="visuallyhidden">Submenu voor “'+r.text+"”</span></span></button>";r.insertAdjacentHTML("afterend",i),n.classList.add("visuallyhidden"),a.addEventListener("pointerenter",function(e){l(this)}),a.addEventListener("pointerleave",function(e){l(this)}),e.querySelector("button").addEventListener("click",function(e){this.parentNode.classList.contains("open")?(this.parentNode.classList.remove("open"),this.parentNode.querySelector("a").setAttribute("aria-expanded","false"),this.parentNode.querySelector("button").setAttribute("aria-expanded","false"),this.parentNode.querySelector("button").classList.remove("open-list"),this.parentNode.querySelector("ul.main-menu__sublist").classList.add("visuallyhidden"),this.parentNode.querySelector("ul.main-menu__sublist").setAttribute("aria-expanded","false")):(this.parentNode.classList.add("open"),this.parentNode.querySelector("a").setAttribute("aria-expanded","true"),this.parentNode.querySelector("button").setAttribute("aria-expanded","true"),this.parentNode.querySelector("button").classList.add("open-list"),this.parentNode.querySelector("ul.main-menu__sublist").classList.remove("visuallyhidden"),this.parentNode.querySelector("ul.main-menu__sublist").setAttribute("aria-expanded","true")),e.preventDefault()})})}t.onkeydown=function(e){27==(e=e||a.event).keyCode&&u()},t.addEventListener("click",function(e){r===e.target||r.contains(e.target)||u()}),!!a.MSInputMethodContext&&!!t.documentMode||(a.addEventListener("load",function(){d(a.innerWidth)}),a.addEventListener("resize",function(){d(a.innerWidth)}));var c=!1,p=e(".btn--close");e(".stepchart__button").on("focus",function(t){c=!0;var a=e(this).parent().find(".stepchart__description");h(a)}).on("click",function(t){if(!1===c){var a=e(this).parent().find(".stepchart__description");h(a)}c=!1});var h=function(t){var n=e(a).width();"true"===t.attr("aria-hidden")?(n>=560&&(e(".stepchart__description[aria-hidden=false]").attr("aria-hidden","true"),e(".show-popover").removeClass("show-popover")),t.attr("aria-hidden","false"),t.parent().addClass("show-popover")):(t.attr("aria-hidden","true"),t.parent().removeClass("show-popover"))};p.on("click",function(){console.log("clickc"),e(this).parent().attr("aria-hidden","true")}),e(a).resize(function(){e(a).width()>=560&&(e(".stepchart__description[aria-hidden=false]").attr("aria-hidden","true"),e(".show-popover").removeClass("show-popover"))}),e(t).on("mouseup",function(t){var a=e(".stepchart__item.show-popover");a.is(t.target)||0!==a.has(t.target).length||(a.removeClass("show-popover"),a.find(".stepchart__description").attr("aria-hidden","true"))});const v=jQuery(".collapsetoggle button"),y=jQuery(".section--video");v.on("click",function(){const e=jQuery(this).parent().next();if("true"===jQuery(this).attr("aria-expanded")){console.log("ding is uitgeklapt");const t=jQuery('.collapsetoggle button[aria-expanded="true"]'),a=jQuery(".collapsible");t&&(t.attr("aria-expanded","false"),a.attr("hidden","hidden")),jQuery(this).attr("aria-expanded","true"),e.removeAttr("hidden"),y.hasClass("show-overlay")||y.addClass("show-overlay")}else console.log("ding NIET uitgeklapt"),jQuery(this).attr("aria-expanded","false"),e.attr("hidden","hidden"),y.removeClass("show-overlay")}),jQuery(t).on("mouseup",function(e){if(y.hasClass("show-overlay")){const t=jQuery(".video__video"),a=jQuery('.collapsetoggle button[aria-expanded="true"]'),n=jQuery(".collapsible");t.is(e.target)||0!==t.has(e.target).length||(y.removeClass("show-overlay"),a.attr("aria-expanded","false"),n.attr("hidden","hidden"))}})})}(jQuery,document,window);