$(".banner-carousel").owlCarousel({
    rtl: !0,
    loop: !0,
    margin: 0,
    nav: !0,
    dots: !0,
    autoplay: 5000,
    smartSpeed: 3000,
    navText: ['<i class="las la-arrow-right"></i>', '<i class="las la-arrow-left"></i>'],
    responsive: {0: {items: 1}, 600: {items: 1}, 1000: {items: 1}}
});
$(".first-home").owlCarousel({
    rtl: !0,
    loop: 0,
    margin: 10,
    nav: !0,
    dots: !1,
    navText: ['<i class="las la-arrow-right" aria-hidden="true"></i>', '<i class="las la-arrow-left" aria-hidden="true"></i>'],
    responsive: {0: {items: 2}, 600: {items: 3}, 1000: {items: 4}}
});
var owl = $(".second-home").owlCarousel({
    rtl: !0,
    loop: 0,
    margin: 10,
    nav: !0,
    dots: !1,
    navText: ['<i class="las la-arrow-right" aria-hidden="true"></i>  السابق ', 'التالي  <i class="las la-arrow-left" aria-hidden="true"></i> '],
    responsive: {0: {items: 2}, 600: {items: 3}, 1000: {items: 4}}
});
$(".owl-filter-bar").on("click", ".item", function () {
    var e = $(this), t = e.data("owl-filter");
    e.addClass("active").siblings().removeClass("active"), owl.owlcarousel2_filter(t)
});
var owl2 = $(".third-home").owlCarousel({
    rtl: !0,
    loop: 0,
    margin: 10,
    nav: !0,
    dots: !1,
    navText: ['<i class="las la-arrow-right" aria-hidden="true"></i>  السابق ', 'التالي  <i class="las la-arrow-left" aria-hidden="true"></i> '],
    responsive: {0: {items: 2}, 600: {items: 3}, 1000: {items: 4}}
});
$(".owl-filter-bar2").on("click", ".item", function () {
    var e = $(this), t = e.data("owl-filter");
    e.addClass("active").siblings().removeClass("active"), owl2.owlcarousel2_filter(t)
}), $(".input-id").rating(), $(".top-header a.btn").click(function () {
    $(".top-header a.btn i").toggleClass("rotate")
}), $(".dropdown>.btn").on("hide.bs.dropdown", function (e) {
    e.clickEvent && e.preventDefault()
}), document.addEventListener("DOMContentLoaded", function () {
    var e = document.getElementsByClassName("input-number-wrapper");

    function t(e, t) {
        var n = +e.value;
        isNaN(n) && (n = 1), n += t, e.value = n > 1 ? n : 1
    }

    Array.prototype.forEach.call(e, function (e) {
        var n = e.getElementsByTagName("input")[0];
        e.getElementsByClassName("increase")[0].addEventListener("click", function () {
            t(n, 1)
        }), e.getElementsByClassName("decrease")[0].addEventListener("click", function () {
            t(n, -1)
        })
    })
}), document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".custom-menu .dropdown-menu").forEach(function (e) {
        e.addEventListener("click", function (e) {
            e.stopPropagation()
        })
    }), window.innerWidth < 992 && (document.querySelectorAll(".navbar .custom-menu .dropdown").forEach(function (e) {
        e.addEventListener("hidden.bs.dropdown", function () {
            this.querySelectorAll(".custom-menu .megasubmenu").forEach(function (e) {
                e.style.display = "none"
            })
        })
    }), document.querySelectorAll(".custom-menu .has-megasubmenu a").forEach(function (e) {
        e.addEventListener("click", function (e) {
            let t = this.nextElementSibling;
            t && t.classList.contains("megasubmenu") && (e.preventDefault(), "block" == t.style.display ? t.style.display = "none" : t.style.display = "block")
        })
    }))
});
var btn = $("#button");
$(window).scroll(function () {
    $(window).scrollTop() > 300 ? btn.addClass("show") : btn.removeClass("show")
}), btn.on("click", function (e) {
    e.preventDefault(), $("html, body").animate({scrollTop: 0}, "300")
}), $(document).ready(function () {
    $(".js-example-basic-single").select2({dir: "rtl"})
}), $(".image-box").click(function (e) {
    var t = $(this).children("img");
    $(this).siblings().children("input").trigger("click"), $(this).siblings().children("input").change(function () {
        var e = new FileReader;
        e.onload = function (e) {
            var n = e.target.result;
            $(t).attr("src", n), t.parent().css("background", "transparent"), t.show(), t.siblings("p").hide()
        }, e.readAsDataURL(this.files[0])
    })
}), document.addEventListener("DOMContentLoaded", init, !1);
var AttachmentArray = [], arrCounter = 0, filesCounterAlertStatus = !1, ul = document.createElement("ul");

function init() {
    document.querySelector("#images").addEventListener("change", handleFileSelect, !1)
}

function handleFileSelect(e) {
    if (e.target.files) {
        for (var t, n = e.target.files, a = 0; t = n[a]; a++) {
            var i = new FileReader;
            i.onload = function (e) {
                return function (t) {
                    ApplyFileValidationRules(e), RenderThumbnail(t, e), FillAttachmentArray(t, e)
                }
            }(t), i.readAsDataURL(t)
        }
        document.getElementById("images").addEventListener("change", handleFileSelect, !1)
    }
}

function ApplyFileValidationRules(t) {
    return 0 == CheckFileType(t.type) ? (alert("الملف (" + t.name + ") يجب ان يكون نوع الملفات jpg/png/gif"), void e.preventDefault()) : 0 == CheckFileSize(t.size) ? (alert("الملف (" + t.name + ") الحجم الاقصى للصورة هو 30 ميجا"), void e.preventDefault()) : 0 == CheckFilesCount(AttachmentArray) ? (filesCounterAlertStatus || (filesCounterAlertStatus = !0, alert("اكبر عدد الصور هو 10 صور")), void e.preventDefault()) : void 0
}

function CheckFileType(e) {
    return "image/jpeg" == e || "image/png" == e || "image/gif" == e
}

function CheckFileSize(e) {
    return e < 1e6
}

function CheckFilesCount(e) {
    for (var t = 0, n = 0; n < e.length; n++) void 0 !== e[n] && t++;
    return !(t > 9)
}

function RenderThumbnail(e, t) {
    var n = document.createElement("li");
    ul.appendChild(n), n.innerHTML = ['<div class="img-wrap"> <span class="close">&times;</span><img class="thumb" src="', e.target.result, '" title="', escape(t.name), '" data-id="', t.name, '"/></div>'].join("");
    var a = document.createElement("div");
    a.className = "FileNameCaptionStyle", n.appendChild(a), a.innerHTML = [t.name].join(""), document.getElementById("Filelist").insertBefore(ul, null)
}

function FillAttachmentArray(e, t) {
    AttachmentArray[arrCounter] = {
        AttachmentType: 1,
        ObjectType: 1,
        FileName: t.name,
        FileDescription: "Attachment",
        NoteText: "",
        MimeType: t.type,
        Content: e.target.result.split("base64,")[1],
        FileSizeInBytes: t.size
    }, arrCounter += 1
}

ul.className = "thumb-Images", ul.id = "imgList", jQuery(function (e) {
    e("div").on("click", ".img-wrap .close", function () {
        var t = e(this).closest(".img-wrap").find("img").data("id"), n = AttachmentArray.map(function (e) {
            return e.FileName
        }).indexOf(t);
        -1 !== n && AttachmentArray.splice(n, 1), e(this).parent().find("img").not().remove(), e(this).parent().find("div").not().remove(), e(this).parent().parent().find("div").not().remove();
        for (var a = document.querySelectorAll("#imgList li"), i = 0; li = a[i]; i++) "" == li.innerHTML && li.parentNode.removeChild(li)
    })
});
var fadeTime = 300;

function recalculateCart(e) {
    var t = 0;
    $(".basket-product").each(function () {
        t += parseFloat($(this).children(".subtotal").text())
    });
    var n = t;
    e ? $(".total-value").fadeOut(fadeTime, function () {
        $("#basket-total").html(n.toFixed(2)), $(".total-value").fadeIn(fadeTime)
    }) : $(".final-value").fadeOut(fadeTime, function () {
        $("#basket-subtotal").html(t.toFixed(2)), $("#basket-total").html(n.toFixed(2)), 0 == n ? $(".checkout-cta").fadeOut(fadeTime) : $(".checkout-cta").fadeIn(fadeTime), $(".final-value").fadeIn(fadeTime)
    })
}

function updateQuantity(e) {
    var t = $(e).parent().parent(), n = parseFloat(t.children(".price").text()), a = $(e).val(), i = n * a;
    t.children(".subtotal").each(function () {
        $(this).fadeOut(fadeTime, function () {
            $(this).text(i.toFixed(2)), recalculateCart(), $(this).fadeIn(fadeTime)
        })
    }), t.find(".item-quantity").text(a), updateSumItems()
}

function updateSumItems() {
    var e = 0;
    $(".quantity input").each(function () {
        e += parseInt($(this).val())
    }), $(".total-items").text(e)
}

function removeItem(e) {
    // var t = $(e).parent().parent();
    // t.slideUp(fadeTime, function () {
    //     t.remove(), recalculateCart(), updateSumItems()
    // })
}

function filterSelection(e) {
    var t, n;
    for (t = document.getElementsByClassName("filterDiv"), "all" == e && (e = ""), n = 0; n < t.length; n++) w3RemoveClass(t[n], "show"), t[n].className.indexOf(e) > -1 && w3AddClass(t[n], "show")
}

function w3AddClass(e, t) {
    var n, a, i;
    for (a = e.className.split(" "), i = t.split(" "), n = 0; n < i.length; n++) -1 == a.indexOf(i[n]) && (e.className += " " + i[n])
}

function w3RemoveClass(e, t) {
    var n, a, i;
    for (a = e.className.split(" "), i = t.split(" "), n = 0; n < i.length; n++) for (; a.indexOf(i[n]) > -1;) a.splice(a.indexOf(i[n]), 1);
    e.className = a.join(" ")
}

$(".quantity input").change(function () {
    updateQuantity(this)
}), $(".remove button").click(function () {
    removeItem(this)
}), $(document).ready(function () {
    updateSumItems()
}), $(document).ready(function () {
    $(".example").DataTable({
        scrollX: !0,
        language: {
            lengthMenu: "عرض _MENU_ ",
            search: " البحث",
            oPaginate: {sFirst: "الاولى", sLast: "الاخيرة", sNext: "التالي", sPrevious: "السابق"},
            zeroRecords: "لا يوجد ما يتم عرضة",
            info: "عرض محتوى صفحة _PAGE_ من _PAGES_",
            infoEmpty: "لا يوجد ما يتم عرضة",
            infoFiltered: "(البحث من _MAX_ كل المعروض)"
        }
    })
}), $(document).ready(function () {
    $("#myInput").on("keyup", function () {
        var e = $(this).val().toLowerCase();
        $("#myTable .supplier-name").filter(function () {
            $("#myTable").toggle($(this).text().toLowerCase().indexOf(e) > -1)
        })
    })
}), $(document).ready(function () {
    lightGallery(document.getElementById("lightgallery"));
    const e = document.querySelector(".progress-done");
    setTimeout(() => {
        e.style.opacity = 1, e.style.width = e.getAttribute("data-done") + "%"
    }, 500)
}), $(".delete").click(function () {
    $(this).parent().parent().parent().fadeOut(500, function () {
        $(this).remove()
    })
}), $(document).ready(function () {
    $(".example2").DataTable({
        bPaginate: !1,
        dom: "Bfrtip",
        language: {
            lengthMenu: "عرض _MENU_ ",
            search: " البحث",
            oPaginate: {sFirst: "الاولى", sLast: "الاخيرة", sNext: "التالي", sPrevious: "السابق"},
            zeroRecords: "لا يوجد ما يتم عرضة",
            info: "",
            infoEmpty: "لا يوجد ما يتم عرضة",
            infoFiltered: "(البحث من _MAX_ كل المعروض)"
        },
        buttons: [{extend: "colvis", text: "المقارنات", postfixButtons: [{extend: "colvisRestore", text: "ارجاع"}]}]
    })
}), filterSelection("all"), $(".btn").click(function () {
    $(".btn").removeClass("active"), $(this).addClass("active")
}), $(function () {
    "use strict";
    var e = $("body");
    e.on("keyup", ".verify-input", function (t) {
        var n = t.which, a = $(t.target).next("input");
        return 9 != n && (n < 48 || n > 57) ? (t.preventDefault(), !1) : 9 === n || (a && a.length || (a = e.find("input").eq(0)), void a.select().focus())
    }), e.on("keydown", ".verify-input", function (e) {
        var t = e.which;
        return 9 === t || t >= 48 && t <= 57 || (e.preventDefault(), !1)
    }), e.on("click", ".verify-input", function (e) {
        $(e.target).select()
    })
});
