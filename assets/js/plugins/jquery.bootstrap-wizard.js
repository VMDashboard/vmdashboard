/*!
 * jQuery twitter bootstrap wizard plugin
 * Examples and documentation at: http://github.com/VinceG/twitter-bootstrap-wizard
 * version 1.4.2
 * Requires jQuery v1.3.2 or later
 * Supports Bootstrap 2.2.x, 2.3.x, 3.0
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 * Authors: Vadim Vincent Gabriel (http://vadimg.com), Jason Gill (www.gilluminate.com)
 */
;
! function(n) {
  n.fn.bootstrapWizard = function(t) {
    if ("string" == typeof t) {
      var i = Array.prototype.slice.call(arguments, 1);
      return 1 === i.length && i.toString(), this.data("bootstrapWizard")[t](i)
    }
    return this.each(function(i) {
      var e = n(this);
      if (!e.data("bootstrapWizard")) {
        var a = new function(t, i) {
          t = n(t);
          var e = this,
            a = 'li:has([data-toggle="tab"])',
            o = [],
            s = n.extend({}, n.fn.bootstrapWizard.defaults, i),
            r = null,
            l = null;
          this.rebindClick = function(n, t) {
            n.unbind("click", t).bind("click", t)
          }, this.fixNavigationButtons = function() {
            if (r.length || (l.find("a:first").tab("show"), r = l.find(a + ":first")), n(s.previousSelector, t).toggleClass("disabled", e.firstIndex() >= e.currentIndex()), n(s.nextSelector, t).toggleClass("disabled", e.currentIndex() >= e.navigationLength()), n(s.nextSelector, t).toggleClass("hidden", e.currentIndex() >= e.navigationLength() && n(s.finishSelector, t).length > 0), n(s.lastSelector, t).toggleClass("hidden", e.currentIndex() >= e.navigationLength() && n(s.finishSelector, t).length > 0), n(s.finishSelector, t).toggleClass("hidden", e.currentIndex() < e.navigationLength()), n(s.backSelector, t).toggleClass("disabled", 0 == o.length), n(s.backSelector, t).toggleClass("hidden", e.currentIndex() >= e.navigationLength() && n(s.finishSelector, t).length > 0), e.rebindClick(n(s.nextSelector, t), e.next), e.rebindClick(n(s.previousSelector, t), e.previous), e.rebindClick(n(s.lastSelector, t), e.last), e.rebindClick(n(s.firstSelector, t), e.first), e.rebindClick(n(s.finishSelector, t), e.finish), e.rebindClick(n(s.backSelector, t), e.back), s.onTabShow && "function" == typeof s.onTabShow && !1 === s.onTabShow(r, l, e.currentIndex())) return !1
          }, this.next = function(n) {
            if (t.hasClass("last")) return !1;
            if (s.onNext && "function" == typeof s.onNext && !1 === s.onNext(r, l, e.nextIndex())) return !1;
            var i = e.currentIndex(),
              d = e.nextIndex();
            d > e.navigationLength() || (o.push(i), l.find(a + (s.withVisible ? ":visible" : "") + ":eq(" + d + ") a").tab("show"))
          }, this.previous = function(n) {
            if (t.hasClass("first")) return !1;
            if (s.onPrevious && "function" == typeof s.onPrevious && !1 === s.onPrevious(r, l, e.previousIndex())) return !1;
            var i = e.currentIndex(),
              d = e.previousIndex();
            d < 0 || (o.push(i), l.find(a + (s.withVisible ? ":visible" : "") + ":eq(" + d + ") a").tab("show"))
          }, this.first = function(n) {
            return (!s.onFirst || "function" != typeof s.onFirst || !1 !== s.onFirst(r, l, e.firstIndex())) && !t.hasClass("disabled") && (o.push(e.currentIndex()), void l.find(a + ":eq(0) a").tab("show"))
          }, this.last = function(n) {
            return (!s.onLast || "function" != typeof s.onLast || !1 !== s.onLast(r, l, e.lastIndex())) && !t.hasClass("disabled") && (o.push(e.currentIndex()), void l.find(a + ":eq(" + e.navigationLength() + ") a").tab("show"))
          }, this.finish = function(n) {
            s.onFinish && "function" == typeof s.onFinish && s.onFinish(r, l, e.lastIndex())
          }, this.back = function() {
            if (0 == o.length) return null;
            var n = o.pop();
            if (s.onBack && "function" == typeof s.onBack && !1 === s.onBack(r, l, n)) return o.push(n), !1;
            t.find(a + ":eq(" + n + ") a").tab("show")
          }, this.currentIndex = function() {
            return l.find(a + (s.withVisible ? ":visible" : "")).index(r)
          }, this.firstIndex = function() {
            return 0
          }, this.lastIndex = function() {
            return e.navigationLength()
          }, this.getIndex = function(n) {
            return l.find(a + (s.withVisible ? ":visible" : "")).index(n)
          }, this.nextIndex = function() {
            var n = this.currentIndex(),
              t = null;
            do {
              n++, t = l.find(a + (s.withVisible ? ":visible" : "") + ":eq(" + n + ")")
            } while (t && t.hasClass("disabled"));
            return n
          }, this.previousIndex = function() {
            var n = this.currentIndex(),
              t = null;
            do {
              n--, t = l.find(a + (s.withVisible ? ":visible" : "") + ":eq(" + n + ")")
            } while (t && t.hasClass("disabled"));
            return n
          }, this.navigationLength = function() {
            return l.find(a + (s.withVisible ? ":visible" : "")).length - 1
          }, this.activeTab = function() {
            return r
          }, this.nextTab = function() {
            return l.find(a + ":eq(" + (e.currentIndex() + 1) + ")").length ? l.find(a + ":eq(" + (e.currentIndex() + 1) + ")") : null
          }, this.previousTab = function() {
            return e.currentIndex() <= 0 ? null : l.find(a + ":eq(" + parseInt(e.currentIndex() - 1) + ")")
          }, this.show = function(n) {
            var i = isNaN(n) ? t.find(a + ' a[href="#' + n + '"]') : t.find(a + ":eq(" + n + ") a");
            i.length > 0 && (o.push(e.currentIndex()), i.tab("show"))
          }, this.disable = function(n) {
            l.find(a + ":eq(" + n + ")").addClass("disabled")
          }, this.enable = function(n) {
            l.find(a + ":eq(" + n + ")").removeClass("disabled")
          }, this.hide = function(n) {
            l.find(a + ":eq(" + n + ")").hide()
          }, this.display = function(n) {
            l.find(a + ":eq(" + n + ")").show()
          }, this.remove = function(t) {
            var i = t[0],
              e = void 0 !== t[1] && t[1],
              o = l.find(a + ":eq(" + i + ")");
            if (e) {
              var s = o.find("a").attr("href");
              n(s).remove()
            }
            o.remove()
          };
          var d = function(t) {
              var i = l.find(a),
                o = i.index(n(t.currentTarget).parent(a)),
                d = n(i[o]);
              if (s.onTabClick && "function" == typeof s.onTabClick && !1 === s.onTabClick(r, l, e.currentIndex(), o, d)) return !1
            },
            u = function(t) {
              var i = n(t.target).parent(),
                o = l.find(a).index(i);
              return !i.hasClass("disabled") && (!s.onTabChange || "function" != typeof s.onTabChange || !1 !== s.onTabChange(r, l, e.currentIndex(), o)) && (r = i, void e.fixNavigationButtons())
            };
          this.resetWizard = function() {
            n('a[data-toggle="tab"]', l).off("click", d), n('a[data-toggle="tab"]', l).off("show show.bs.tab", u), l = t.find("ul:first", t), r = l.find(a + ".active", t), n('a[data-toggle="tab"]', l).on("click", d), n('a[data-toggle="tab"]', l).on("show show.bs.tab", u), e.fixNavigationButtons()
          }, l = t.find("ul:first", t), r = l.find(a + ".active", t), l.hasClass(s.tabClass) || l.addClass(s.tabClass), s.onInit && "function" == typeof s.onInit && s.onInit(r, l, 0), s.onShow && "function" == typeof s.onShow && s.onShow(r, l, e.nextIndex()), n('a[data-toggle="tab"]', l).on("click", d), n('a[data-toggle="tab"]', l).on("show show.bs.tab", u)
        }(e, t);
        e.data("bootstrapWizard", a), a.fixNavigationButtons()
      }
    })
  }, n.fn.bootstrapWizard.defaults = {
    withVisible: !0,
    tabClass: "nav nav-pills",
    nextSelector: ".wizard li.next",
    previousSelector: ".wizard li.previous",
    firstSelector: ".wizard li.first",
    lastSelector: ".wizard li.last",
    finishSelector: ".wizard li.finish",
    backSelector: ".wizard li.back",
    onShow: null,
    onInit: null,
    onNext: null,
    onPrevious: null,
    onLast: null,
    onFirst: null,
    onFinish: null,
    onBack: null,
    onTabChange: null,
    onTabClick: null,
    onTabShow: null
  }
}(jQuery);