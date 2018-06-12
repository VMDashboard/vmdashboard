/* ========================================================================
 * bootstrap-switch - v3.3.2
 * http://www.bootstrap-switch.org
 * ========================================================================
 * Copyright 2012-2013 Mattia Larentis
 *
 * ========================================================================
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================================
 */
(function() {
  var t = [].slice;
  ! function(e, i) {
    "use strict";
    var s;
    s = function() {
      function t(t, i) {
        var s, o, n;
        null == i && (i = {}), this.$element = e(t), this.options = e.extend({}, e.fn.bootstrapSwitch.defaults, {
          state: this.$element.is(":checked"),
          size: this.$element.data("size"),
          animate: this.$element.data("animate"),
          disabled: this.$element.is(":disabled"),
          readonly: this.$element.is("[readonly]"),
          indeterminate: this.$element.data("indeterminate"),
          inverse: this.$element.data("inverse"),
          radioAllOff: this.$element.data("radio-all-off"),
          onColor: this.$element.data("on-color"),
          offColor: this.$element.data("off-color"),
          onText: this.$element.data("on-text"),
          offText: this.$element.data("off-text"),
          labelText: this.$element.data("label-text"),
          handleWidth: this.$element.data("handle-width"),
          labelWidth: this.$element.data("label-width"),
          baseClass: this.$element.data("base-class"),
          wrapperClass: this.$element.data("wrapper-class")
        }, i), this.prevOptions = {}, this.$wrapper = e("<div>", {
          class: (s = this, function() {
            var t;
            return (t = ["" + s.options.baseClass].concat(s._getClasses(s.options.wrapperClass))).push(s.options.state ? s.options.baseClass + "-on" : s.options.baseClass + "-off"), null != s.options.size && t.push(s.options.baseClass + "-" + s.options.size), s.options.disabled && t.push(s.options.baseClass + "-disabled"), s.options.readonly && t.push(s.options.baseClass + "-readonly"), s.options.indeterminate && t.push(s.options.baseClass + "-indeterminate"), s.options.inverse && t.push(s.options.baseClass + "-inverse"), s.$element.attr("id") && t.push(s.options.baseClass + "-id-" + s.$element.attr("id")), t.join(" ")
          })()
        }), this.$container = e("<div>", {
          class: this.options.baseClass + "-container"
        }), this.$on = e("<span>", {
          html: this.options.onText,
          class: this.options.baseClass + "-handle-on " + this.options.baseClass + "-" + this.options.onColor
        }), this.$off = e("<span>", {
          html: this.options.offText,
          class: this.options.baseClass + "-handle-off " + this.options.baseClass + "-" + this.options.offColor
        }), this.$label = e("<span>", {
          html: this.options.labelText,
          class: this.options.baseClass + "-label"
        }), this.$element.on("init.bootstrapSwitch", (o = this, function() {
          return o.options.onInit.apply(t, arguments)
        })), this.$element.on("switchChange.bootstrapSwitch", (n = this, function(i) {
          if (!1 === n.options.onSwitchChange.apply(t, arguments)) return n.$element.is(":radio") ? e("[name='" + n.$element.attr("name") + "']").trigger("previousState.bootstrapSwitch", !0) : n.$element.trigger("previousState.bootstrapSwitch", !0)
        })), this.$container = this.$element.wrap(this.$container).parent(), this.$wrapper = this.$container.wrap(this.$wrapper).parent(), this.$element.before(this.options.inverse ? this.$off : this.$on).before(this.$label).before(this.options.inverse ? this.$on : this.$off), this.options.indeterminate && this.$element.prop("indeterminate", !0), this._init(), this._elementHandlers(), this._handleHandlers(), this._labelHandlers(), this._formHandler(), this._externalLabelHandler(), this.$element.trigger("init.bootstrapSwitch", this.options.state)
      }
      return t.prototype._constructor = t, t.prototype.setPrevOptions = function() {
        return this.prevOptions = e.extend(!0, {}, this.options)
      }, t.prototype.state = function(t, i) {
        return void 0 === t ? this.options.state : this.options.disabled || this.options.readonly ? this.$element : this.options.state && !this.options.radioAllOff && this.$element.is(":radio") ? this.$element : (this.$element.is(":radio") ? e("[name='" + this.$element.attr("name") + "']").trigger("setPreviousOptions.bootstrapSwitch") : this.$element.trigger("setPreviousOptions.bootstrapSwitch"), this.options.indeterminate && this.indeterminate(!1), t = !!t, this.$element.prop("checked", t).trigger("change.bootstrapSwitch", i), this.$element)
      }, t.prototype.toggleState = function(t) {
        return this.options.disabled || this.options.readonly ? this.$element : this.options.indeterminate ? (this.indeterminate(!1), this.state(!0)) : this.$element.prop("checked", !this.options.state).trigger("change.bootstrapSwitch", t)
      }, t.prototype.size = function(t) {
        return void 0 === t ? this.options.size : (null != this.options.size && this.$wrapper.removeClass(this.options.baseClass + "-" + this.options.size), t && this.$wrapper.addClass(this.options.baseClass + "-" + t), this._width(), this._containerPosition(), this.options.size = t, this.$element)
      }, t.prototype.animate = function(t) {
        return void 0 === t ? this.options.animate : (t = !!t) === this.options.animate ? this.$element : this.toggleAnimate()
      }, t.prototype.toggleAnimate = function() {
        return this.options.animate = !this.options.animate, this.$wrapper.toggleClass(this.options.baseClass + "-animate"), this.$element
      }, t.prototype.disabled = function(t) {
        return void 0 === t ? this.options.disabled : (t = !!t) === this.options.disabled ? this.$element : this.toggleDisabled()
      }, t.prototype.toggleDisabled = function() {
        return this.options.disabled = !this.options.disabled, this.$element.prop("disabled", this.options.disabled), this.$wrapper.toggleClass(this.options.baseClass + "-disabled"), this.$element
      }, t.prototype.readonly = function(t) {
        return void 0 === t ? this.options.readonly : (t = !!t) === this.options.readonly ? this.$element : this.toggleReadonly()
      }, t.prototype.toggleReadonly = function() {
        return this.options.readonly = !this.options.readonly, this.$element.prop("readonly", this.options.readonly), this.$wrapper.toggleClass(this.options.baseClass + "-readonly"), this.$element
      }, t.prototype.indeterminate = function(t) {
        return void 0 === t ? this.options.indeterminate : (t = !!t) === this.options.indeterminate ? this.$element : this.toggleIndeterminate()
      }, t.prototype.toggleIndeterminate = function() {
        return this.options.indeterminate = !this.options.indeterminate, this.$element.prop("indeterminate", this.options.indeterminate), this.$wrapper.toggleClass(this.options.baseClass + "-indeterminate"), this._containerPosition(), this.$element
      }, t.prototype.inverse = function(t) {
        return void 0 === t ? this.options.inverse : (t = !!t) === this.options.inverse ? this.$element : this.toggleInverse()
      }, t.prototype.toggleInverse = function() {
        var t, e;
        return this.$wrapper.toggleClass(this.options.baseClass + "-inverse"), e = this.$on.clone(!0), t = this.$off.clone(!0), this.$on.replaceWith(t), this.$off.replaceWith(e), this.$on = t, this.$off = e, this.options.inverse = !this.options.inverse, this.$element
      }, t.prototype.onColor = function(t) {
        var e;
        return e = this.options.onColor, void 0 === t ? e : (null != e && this.$on.removeClass(this.options.baseClass + "-" + e), this.$on.addClass(this.options.baseClass + "-" + t), this.options.onColor = t, this.$element)
      }, t.prototype.offColor = function(t) {
        var e;
        return e = this.options.offColor, void 0 === t ? e : (null != e && this.$off.removeClass(this.options.baseClass + "-" + e), this.$off.addClass(this.options.baseClass + "-" + t), this.options.offColor = t, this.$element)
      }, t.prototype.onText = function(t) {
        return void 0 === t ? this.options.onText : (this.$on.html(t), this._width(), this._containerPosition(), this.options.onText = t, this.$element)
      }, t.prototype.offText = function(t) {
        return void 0 === t ? this.options.offText : (this.$off.html(t), this._width(), this._containerPosition(), this.options.offText = t, this.$element)
      }, t.prototype.labelText = function(t) {
        return void 0 === t ? this.options.labelText : (this.$label.html(t), this._width(), this.options.labelText = t, this.$element)
      }, t.prototype.handleWidth = function(t) {
        return void 0 === t ? this.options.handleWidth : (this.options.handleWidth = t, this._width(), this._containerPosition(), this.$element)
      }, t.prototype.labelWidth = function(t) {
        return void 0 === t ? this.options.labelWidth : (this.options.labelWidth = t, this._width(), this._containerPosition(), this.$element)
      }, t.prototype.baseClass = function(t) {
        return this.options.baseClass
      }, t.prototype.wrapperClass = function(t) {
        return void 0 === t ? this.options.wrapperClass : (t || (t = e.fn.bootstrapSwitch.defaults.wrapperClass), this.$wrapper.removeClass(this._getClasses(this.options.wrapperClass).join(" ")), this.$wrapper.addClass(this._getClasses(t).join(" ")), this.options.wrapperClass = t, this.$element)
      }, t.prototype.radioAllOff = function(t) {
        return void 0 === t ? this.options.radioAllOff : (t = !!t) === this.options.radioAllOff ? this.$element : (this.options.radioAllOff = t, this.$element)
      }, t.prototype.onInit = function(t) {
        return void 0 === t ? this.options.onInit : (t || (t = e.fn.bootstrapSwitch.defaults.onInit), this.options.onInit = t, this.$element)
      }, t.prototype.onSwitchChange = function(t) {
        return void 0 === t ? this.options.onSwitchChange : (t || (t = e.fn.bootstrapSwitch.defaults.onSwitchChange), this.options.onSwitchChange = t, this.$element)
      }, t.prototype.destroy = function() {
        var t;
        return (t = this.$element.closest("form")).length && t.off("reset.bootstrapSwitch").removeData("bootstrap-switch"), this.$container.children().not(this.$element).remove(), this.$element.unwrap().unwrap().off(".bootstrapSwitch").removeData("bootstrap-switch"), this.$element
      }, t.prototype._width = function() {
        var t, e, i;
        return (t = this.$on.add(this.$off)).add(this.$label).css("width", ""), e = "auto" === this.options.handleWidth ? Math.max(this.$on.width(), this.$off.width()) : this.options.handleWidth, t.width(e), this.$label.width((i = this, function(t, s) {
          return "auto" !== i.options.labelWidth ? i.options.labelWidth : s < e ? e : s
        })), this._handleWidth = this.$on.outerWidth(), this._labelWidth = this.$label.outerWidth(), this.$container.width(2 * this._handleWidth + this._labelWidth), this.$wrapper.width(this._handleWidth + this._labelWidth)
      }, t.prototype._containerPosition = function(t, e) {
        var i;
        if (null == t && (t = this.options.state), this.$container.css("margin-left", (i = this, function() {
            var e;
            return e = [0, "-" + i._handleWidth + "px"], i.options.indeterminate ? "-" + i._handleWidth / 2 + "px" : t ? i.options.inverse ? e[1] : e[0] : i.options.inverse ? e[0] : e[1]
          })), e) return setTimeout(function() {
          return e()
        }, 50)
      }, t.prototype._init = function() {
        var t, e, s, o;
        return s = this, t = function() {
          return s.setPrevOptions(), s._width(), s._containerPosition(null, function() {
            if (s.options.animate) return s.$wrapper.addClass(s.options.baseClass + "-animate")
          })
        }, this.$wrapper.is(":visible") ? t() : e = i.setInterval((o = this, function() {
          if (o.$wrapper.is(":visible")) return t(), i.clearInterval(e)
        }), 50)
      }, t.prototype._elementHandlers = function() {
        return this.$element.on({
          "setPreviousOptions.bootstrapSwitch": (a = this, function(t) {
            return a.setPrevOptions()
          }),
          "previousState.bootstrapSwitch": (n = this, function(t) {
            return n.options = n.prevOptions, n.options.indeterminate && n.$wrapper.addClass(n.options.baseClass + "-indeterminate"), n.$element.prop("checked", n.options.state).trigger("change.bootstrapSwitch", !0)
          }),
          "change.bootstrapSwitch": (o = this, function(t, i) {
            var s;
            if (t.preventDefault(), t.stopImmediatePropagation(), s = o.$element.is(":checked"), o._containerPosition(s), s !== o.options.state) return o.options.state = s, o.$wrapper.toggleClass(o.options.baseClass + "-off").toggleClass(o.options.baseClass + "-on"), i ? void 0 : (o.$element.is(":radio") && e("[name='" + o.$element.attr("name") + "']").not(o.$element).prop("checked", !1).trigger("change.bootstrapSwitch", !0), o.$element.trigger("switchChange.bootstrapSwitch", [s]))
          }),
          "focus.bootstrapSwitch": (s = this, function(t) {
            return t.preventDefault(), s.$wrapper.addClass(s.options.baseClass + "-focused")
          }),
          "blur.bootstrapSwitch": (i = this, function(t) {
            return t.preventDefault(), i.$wrapper.removeClass(i.options.baseClass + "-focused")
          }),
          "keydown.bootstrapSwitch": (t = this, function(e) {
            if (e.which && !t.options.disabled && !t.options.readonly) switch (e.which) {
              case 37:
                return e.preventDefault(), e.stopImmediatePropagation(), t.state(!1);
              case 39:
                return e.preventDefault(), e.stopImmediatePropagation(), t.state(!0)
            }
          })
        });
        var t, i, s, o, n, a
      }, t.prototype._handleHandlers = function() {
        var t, e;
        return this.$on.on("click.bootstrapSwitch", (t = this, function(e) {
          return e.preventDefault(), e.stopPropagation(), t.state(!1), t.$element.trigger("focus.bootstrapSwitch")
        })), this.$off.on("click.bootstrapSwitch", (e = this, function(t) {
          return t.preventDefault(), t.stopPropagation(), e.state(!0), e.$element.trigger("focus.bootstrapSwitch")
        }))
      }, t.prototype._labelHandlers = function() {
        return this.$label.on({
          click: function(t) {
            return t.stopPropagation()
          },
          "mousedown.bootstrapSwitch touchstart.bootstrapSwitch": (s = this, function(t) {
            if (!(s._dragStart || s.options.disabled || s.options.readonly)) return t.preventDefault(), t.stopPropagation(), s._dragStart = (t.pageX || t.originalEvent.touches[0].pageX) - parseInt(s.$container.css("margin-left"), 10), s.options.animate && s.$wrapper.removeClass(s.options.baseClass + "-animate"), s.$element.trigger("focus.bootstrapSwitch")
          }),
          "mousemove.bootstrapSwitch touchmove.bootstrapSwitch": (i = this, function(t) {
            var e;
            if (null != i._dragStart && (t.preventDefault(), !((e = (t.pageX || t.originalEvent.touches[0].pageX) - i._dragStart) < -i._handleWidth || e > 0))) return i._dragEnd = e, i.$container.css("margin-left", i._dragEnd + "px")
          }),
          "mouseup.bootstrapSwitch touchend.bootstrapSwitch": (e = this, function(t) {
            var i;
            if (e._dragStart) return t.preventDefault(), e.options.animate && e.$wrapper.addClass(e.options.baseClass + "-animate"), e._dragEnd ? (i = e._dragEnd > -e._handleWidth / 2, e._dragEnd = !1, e.state(e.options.inverse ? !i : i)) : e.state(!e.options.state), e._dragStart = !1
          }),
          "mouseleave.bootstrapSwitch": (t = this, function(e) {
            return t.$label.trigger("mouseup.bootstrapSwitch")
          })
        });
        var t, e, i, s
      }, t.prototype._externalLabelHandler = function() {
        var t, e;
        return (t = this.$element.closest("label")).on("click", (e = this, function(i) {
          if (i.preventDefault(), i.stopImmediatePropagation(), i.target === t[0]) return e.toggleState()
        }))
      }, t.prototype._formHandler = function() {
        var t;
        if (!(t = this.$element.closest("form")).data("bootstrap-switch")) return t.on("reset.bootstrapSwitch", function() {
          return i.setTimeout(function() {
            return t.find("input").filter(function() {
              return e(this).data("bootstrap-switch")
            }).each(function() {
              return e(this).bootstrapSwitch("state", this.checked)
            })
          }, 1)
        }).data("bootstrap-switch", !0)
      }, t.prototype._getClasses = function(t) {
        var i, s, o, n;
        if (!e.isArray(t)) return [this.options.baseClass + "-" + t];
        for (s = [], o = 0, n = t.length; o < n; o++) i = t[o], s.push(this.options.baseClass + "-" + i);
        return s
      }, t
    }(), e.fn.bootstrapSwitch = function() {
      var i, o, n;
      return o = arguments[0], i = 2 <= arguments.length ? t.call(arguments, 1) : [], n = this, this.each(function() {
        var t, a;
        if ((a = (t = e(this)).data("bootstrap-switch")) || t.data("bootstrap-switch", a = new s(this, o)), "string" == typeof o) return n = a[o].apply(a, i)
      }), n
    }, e.fn.bootstrapSwitch.Constructor = s, e.fn.bootstrapSwitch.defaults = {
      state: !0,
      size: null,
      animate: !0,
      disabled: !1,
      readonly: !1,
      indeterminate: !1,
      inverse: !1,
      radioAllOff: !1,
      onColor: "primary",
      offColor: "default",
      onText: "ON",
      offText: "OFF",
      labelText: "&nbsp;",
      handleWidth: "auto",
      labelWidth: "auto",
      baseClass: "bootstrap-switch",
      wrapperClass: "wrapper",
      onInit: function() {},
      onSwitchChange: function() {}
    }
  }(window.jQuery, window)
}).call(this);