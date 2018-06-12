/*



     Creative Tim Modifications

     Lines: 238, 239 was changed from top: 5px to top: 50% and we added margin-top: -13px. In this way the close button will be aligned vertically
     Line:222 - modified when the icon is set, we add the class "alert-with-icon", so there will be enough space for the icon.




*/


/*
 * Project: Bootstrap Notify = v3.1.5
 * Description: Turns standard Bootstrap alerts into "Growl-like" notifications.
 * Author: Mouse0270 aka Robert McIntosh
 * License: MIT License
 * Website: https://github.com/mouse0270/bootstrap-growl
 */

/* global define:false, require: false, jQuery:false */
! function(t) {
  "function" == typeof define && define.amd ? define(["jquery"], t) : "object" == typeof exports ? t(require("jquery")) : t(jQuery)
}(function(t) {
  var s = {
    element: "body",
    position: null,
    type: "info",
    allow_dismiss: !0,
    allow_duplicates: !0,
    newest_on_top: !1,
    showProgressbar: !1,
    placement: {
      from: "top",
      align: "right"
    },
    offset: 20,
    spacing: 10,
    z_index: 1060,
    delay: 5e3,
    timer: 1e3,
    url_target: "_blank",
    mouse_over: null,
    animate: {
      enter: "animated fadeInDown",
      exit: "animated fadeOutUp"
    },
    onShow: null,
    onShown: null,
    onClose: null,
    onClosed: null,
    onClick: null,
    icon_type: "class",
    template: '<div data-notify="container" class="col-11 col-md-4 alert alert-{0}" role="alert"><button type="button" aria-hidden="true" class="close" data-notify="dismiss"><i class="nc-icon nc-simple-remove"></i></button><span data-notify="icon"></span> <span data-notify="title">{1}</span> <span data-notify="message">{2}</span><div class="progress" data-notify="progressbar"><div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div></div><a href="{3}" target="{4}" data-notify="url"></a></div>'
  };

  function i(i, e, n) {
    var a, o, r = {
      content: {
        message: "object" == typeof e ? e.message : e,
        title: e.title ? e.title : "",
        icon: e.icon ? e.icon : "",
        url: e.url ? e.url : "#",
        target: e.target ? e.target : "-"
      }
    };
    n = t.extend(!0, {}, r, n), this.settings = t.extend(!0, {}, s, n), this._defaults = s, "-" === this.settings.content.target && (this.settings.content.target = this.settings.url_target), this.animations = {
      start: "webkitAnimationStart oanimationstart MSAnimationStart animationstart",
      end: "webkitAnimationEnd oanimationend MSAnimationEnd animationend"
    }, "number" == typeof this.settings.offset && (this.settings.offset = {
      x: this.settings.offset,
      y: this.settings.offset
    }), (this.settings.allow_duplicates || !this.settings.allow_duplicates && (a = this, o = !1, t('[data-notify="container"]').each(function(s, i) {
      var e = t(i),
        n = e.find('[data-notify="title"]').html().trim(),
        r = e.find('[data-notify="message"]').html().trim(),
        l = n === t("<div>" + a.settings.content.title + "</div>").html().trim(),
        d = r === t("<div>" + a.settings.content.message + "</div>").html().trim(),
        c = e.hasClass("alert-" + a.settings.type);
      return l && d && c && (o = !0), !o
    }), !o)) && this.init()
  }
  String.format = function() {
    var t = arguments;
    return arguments[0].replace(/(\{\{\d\}\}|\{\d\})/g, function(s) {
      if ("{{" === s.substring(0, 2)) return s;
      var i = parseInt(s.match(/\d/)[0]);
      return t[i + 1]
    })
  }, t.extend(i.prototype, {
    init: function() {
      var t = this;
      this.buildNotify(), this.settings.content.icon && this.setIcon(), "#" != this.settings.content.url && this.styleURL(), this.styleDismiss(), this.placement(), this.bind(), this.notify = {
        $ele: this.$ele,
        update: function(s, i) {
          var e = {};
          "string" == typeof s ? e[s] = i : e = s;
          for (var n in e) switch (n) {
            case "type":
              this.$ele.removeClass("alert-" + t.settings.type), this.$ele.find('[data-notify="progressbar"] > .progress-bar').removeClass("progress-bar-" + t.settings.type), t.settings.type = e[n], this.$ele.addClass("alert-" + e[n]).find('[data-notify="progressbar"] > .progress-bar').addClass("progress-bar-" + e[n]);
              break;
            case "icon":
              var a = this.$ele.find('[data-notify="icon"]');
              "class" === t.settings.icon_type.toLowerCase() ? a.removeClass(t.settings.content.icon).addClass(e[n]) : (a.is("img") || a.find("img"), a.attr("src", e[n])), t.settings.content.icon = e[s];
              break;
            case "progress":
              var o = t.settings.delay - t.settings.delay * (e[n] / 100);
              this.$ele.data("notify-delay", o), this.$ele.find('[data-notify="progressbar"] > div').attr("aria-valuenow", e[n]).css("width", e[n] + "%");
              break;
            case "url":
              this.$ele.find('[data-notify="url"]').attr("href", e[n]);
              break;
            case "target":
              this.$ele.find('[data-notify="url"]').attr("target", e[n]);
              break;
            default:
              this.$ele.find('[data-notify="' + n + '"]').html(e[n])
          }
          var r = this.$ele.outerHeight() + parseInt(t.settings.spacing) + parseInt(t.settings.offset.y);
          t.reposition(r)
        },
        close: function() {
          t.close()
        }
      }
    },
    buildNotify: function() {
      var s = this.settings.content;
      this.$ele = t(String.format(this.settings.template, this.settings.type, s.title, s.message, s.url, s.target)), this.$ele.attr("data-notify-position", this.settings.placement.from + "-" + this.settings.placement.align), this.settings.allow_dismiss || this.$ele.find('[data-notify="dismiss"]').css("display", "none"), (this.settings.delay <= 0 && !this.settings.showProgressbar || !this.settings.showProgressbar) && this.$ele.find('[data-notify="progressbar"]').remove()
    },
    setIcon: function() {
      this.$ele.addClass("alert-with-icon"), "class" === this.settings.icon_type.toLowerCase() ? this.$ele.find('[data-notify="icon"]').addClass(this.settings.content.icon) : this.$ele.find('[data-notify="icon"]').is("img") ? this.$ele.find('[data-notify="icon"]').attr("src", this.settings.content.icon) : this.$ele.find('[data-notify="icon"]').append('<img src="' + this.settings.content.icon + '" alt="Notify Icon" />')
    },
    styleDismiss: function() {
      this.$ele.find('[data-notify="dismiss"]').css({
        position: "absolute",
        right: "10px",
        top: "50%",
        marginTop: "-13px",
        zIndex: this.settings.z_index + 2
      })
    },
    styleURL: function() {
      this.$ele.find('[data-notify="url"]').css({
        backgroundImage: "url(data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7)",
        height: "100%",
        left: 0,
        position: "absolute",
        top: 0,
        width: "100%",
        zIndex: this.settings.z_index + 1
      })
    },
    placement: function() {
      var s = this,
        i = this.settings.offset.y,
        e = {
          display: "inline-block",
          margin: "0px auto",
          position: this.settings.position ? this.settings.position : "body" === this.settings.element ? "fixed" : "absolute",
          transition: "all .5s ease-in-out",
          zIndex: this.settings.z_index
        },
        n = !1,
        a = this.settings;
      switch (t('[data-notify-position="' + this.settings.placement.from + "-" + this.settings.placement.align + '"]:not([data-closing="true"])').each(function() {
        i = Math.max(i, parseInt(t(this).css(a.placement.from)) + parseInt(t(this).outerHeight()) + parseInt(a.spacing))
      }), !0 === this.settings.newest_on_top && (i = this.settings.offset.y), e[this.settings.placement.from] = i + "px", this.settings.placement.align) {
        case "left":
        case "right":
          e[this.settings.placement.align] = this.settings.offset.x + "px";
          break;
        case "center":
          e.left = 0, e.right = 0
      }
      this.$ele.css(e).addClass(this.settings.animate.enter), t.each(Array("webkit-", "moz-", "o-", "ms-", ""), function(t, i) {
        s.$ele[0].style[i + "AnimationIterationCount"] = 1
      }), t(this.settings.element).append(this.$ele), !0 === this.settings.newest_on_top && (i = parseInt(i) + parseInt(this.settings.spacing) + this.$ele.outerHeight(), this.reposition(i)), t.isFunction(s.settings.onShow) && s.settings.onShow.call(this.$ele), this.$ele.one(this.animations.start, function() {
        n = !0
      }).one(this.animations.end, function() {
        s.$ele.removeClass(s.settings.animate.enter), t.isFunction(s.settings.onShown) && s.settings.onShown.call(this)
      }), setTimeout(function() {
        n || t.isFunction(s.settings.onShown) && s.settings.onShown.call(this)
      }, 600)
    },
    bind: function() {
      var s = this;
      if (this.$ele.find('[data-notify="dismiss"]').on("click", function() {
          s.close()
        }), t.isFunction(s.settings.onClick) && this.$ele.on("click", function(t) {
          t.target != s.$ele.find('[data-notify="dismiss"]')[0] && s.settings.onClick.call(this, t)
        }), this.$ele.mouseover(function() {
          t(this).data("data-hover", "true")
        }).mouseout(function() {
          t(this).data("data-hover", "false")
        }), this.$ele.data("data-hover", "false"), this.settings.delay > 0) {
        s.$ele.data("notify-delay", s.settings.delay);
        var i = setInterval(function() {
          var t = parseInt(s.$ele.data("notify-delay")) - s.settings.timer;
          if ("false" === s.$ele.data("data-hover") && "pause" === s.settings.mouse_over || "pause" != s.settings.mouse_over) {
            var e = (s.settings.delay - t) / s.settings.delay * 100;
            s.$ele.data("notify-delay", t), s.$ele.find('[data-notify="progressbar"] > div').attr("aria-valuenow", e).css("width", e + "%")
          }
          t <= -s.settings.timer && (clearInterval(i), s.close())
        }, s.settings.timer)
      }
    },
    close: function() {
      var s = this,
        i = parseInt(this.$ele.css(this.settings.placement.from)),
        e = !1;
      this.$ele.attr("data-closing", "true").addClass(this.settings.animate.exit), s.reposition(i), t.isFunction(s.settings.onClose) && s.settings.onClose.call(this.$ele), this.$ele.one(this.animations.start, function() {
        e = !0
      }).one(this.animations.end, function() {
        t(this).remove(), t.isFunction(s.settings.onClosed) && s.settings.onClosed.call(this)
      }), setTimeout(function() {
        e || (s.$ele.remove(), s.settings.onClosed && s.settings.onClosed(s.$ele))
      }, 600)
    },
    reposition: function(s) {
      var i = this,
        e = '[data-notify-position="' + this.settings.placement.from + "-" + this.settings.placement.align + '"]:not([data-closing="true"])',
        n = this.$ele.nextAll(e);
      !0 === this.settings.newest_on_top && (n = this.$ele.prevAll(e)), n.each(function() {
        t(this).css(i.settings.placement.from, s), s = parseInt(s) + parseInt(i.settings.spacing) + t(this).outerHeight()
      })
    }
  }), t.notify = function(t, s) {
    return new i(this, t, s).notify
  }, t.notifyDefaults = function(i) {
    return s = t.extend(!0, {}, s, i)
  }, t.notifyClose = function(s) {
    void 0 === s || "all" === s ? t("[data-notify]").find('[data-notify="dismiss"]').trigger("click") : "success" === s || "info" === s || "warning" === s || "danger" === s ? t(".alert-" + s + "[data-notify]").find('[data-notify="dismiss"]').trigger("click") : s ? t(s + "[data-notify]").find('[data-notify="dismiss"]').trigger("click") : t('[data-notify-position="' + s + '"]').find('[data-notify="dismiss"]').trigger("click")
  }, t.notifyCloseExcept = function(s) {
    "success" === s || "info" === s || "warning" === s || "danger" === s ? t("[data-notify]").not(".alert-" + s).find('[data-notify="dismiss"]').trigger("click") : t("[data-notify]").not(s).find('[data-notify="dismiss"]').trigger("click")
  }
});