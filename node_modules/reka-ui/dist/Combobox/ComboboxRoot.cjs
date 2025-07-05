'use strict';

const vue = require('vue');
const core = require('@vueuse/core');
const Popper_PopperRoot = require('../Popper/PopperRoot.cjs');
const shared_createContext = require('../shared/createContext.cjs');
const Primitive_usePrimitiveElement = require('../Primitive/usePrimitiveElement.cjs');
const shared_useDirection = require('../shared/useDirection.cjs');
const shared_useFilter = require('../shared/useFilter.cjs');
const Listbox_ListboxRoot = require('../Listbox/ListboxRoot.cjs');

const [injectComboboxRootContext, provideComboboxRootContext] = shared_createContext.createContext("ComboboxRoot");
const _sfc_main = /* @__PURE__ */ vue.defineComponent({
  __name: "ComboboxRoot",
  props: {
    open: { type: Boolean, default: void 0 },
    defaultOpen: { type: Boolean },
    resetSearchTermOnBlur: { type: Boolean, default: true },
    resetSearchTermOnSelect: { type: Boolean, default: true },
    ignoreFilter: { type: Boolean },
    modelValue: {},
    defaultValue: {},
    multiple: { type: Boolean },
    dir: {},
    disabled: { type: Boolean },
    highlightOnHover: { type: Boolean },
    by: {},
    asChild: { type: Boolean },
    as: {},
    name: {},
    required: { type: Boolean }
  },
  emits: ["update:modelValue", "highlight", "update:open"],
  setup(__props, { expose: __expose, emit: __emit }) {
    const props = __props;
    const emits = __emit;
    const { primitiveElement, currentElement: parentElement } = Primitive_usePrimitiveElement.usePrimitiveElement();
    const { multiple, disabled, ignoreFilter, resetSearchTermOnSelect, dir: propDir } = vue.toRefs(props);
    const dir = shared_useDirection.useDirection(propDir);
    const modelValue = core.useVModel(props, "modelValue", emits, {
      defaultValue: props.defaultValue ?? (multiple.value ? [] : void 0),
      passive: props.modelValue === void 0,
      deep: true
    });
    const open = core.useVModel(props, "open", emits, {
      defaultValue: props.defaultOpen,
      passive: props.open === void 0
    });
    async function onOpenChange(val) {
      open.value = val;
      filterSearch.value = "";
      if (val) {
        await vue.nextTick();
        primitiveElement.value?.highlightSelected();
        isUserInputted.value = true;
      } else {
        isUserInputted.value = false;
      }
      inputElement.value?.focus();
      setTimeout(() => {
        if (!val && props.resetSearchTermOnBlur)
          resetSearchTerm.trigger();
      }, 1);
    }
    const resetSearchTerm = core.createEventHook();
    const isUserInputted = vue.ref(false);
    const isVirtual = vue.ref(false);
    const inputElement = vue.ref();
    const triggerElement = vue.ref();
    const highlightedElement = vue.computed(() => primitiveElement.value?.highlightedElement ?? void 0);
    const allItems = vue.ref(/* @__PURE__ */ new Map());
    const allGroups = vue.ref(/* @__PURE__ */ new Map());
    const { contains } = shared_useFilter.useFilter({ sensitivity: "base" });
    const filterSearch = vue.ref("");
    const filterState = vue.computed((oldValue) => {
      if (!filterSearch.value || props.ignoreFilter || isVirtual.value) {
        return {
          count: allItems.value.size,
          items: oldValue?.items ?? /* @__PURE__ */ new Map(),
          groups: oldValue?.groups ?? new Set(allGroups.value.keys())
        };
      }
      let itemCount = 0;
      const filteredItems = /* @__PURE__ */ new Map();
      const filteredGroups = /* @__PURE__ */ new Set();
      for (const [id, value] of allItems.value) {
        const score = contains(value, filterSearch.value);
        filteredItems.set(id, score ? 1 : 0);
        if (score)
          itemCount++;
      }
      for (const [groupId, group] of allGroups.value) {
        for (const itemId of group) {
          if (filteredItems.get(itemId) > 0) {
            filteredGroups.add(groupId);
            break;
          }
        }
      }
      return {
        count: itemCount,
        items: filteredItems,
        groups: filteredGroups
      };
    });
    const inst = vue.getCurrentInstance();
    vue.onMounted(() => {
      if (inst?.exposed) {
        inst.exposed.highlightItem = primitiveElement.value?.highlightItem;
        inst.exposed.highlightFirstItem = primitiveElement.value?.highlightFirstItem;
        inst.exposed.highlightSelected = primitiveElement.value?.highlightSelected;
      }
    });
    __expose({
      filtered: filterState,
      highlightedElement,
      highlightItem: primitiveElement.value?.highlightItem,
      highlightFirstItem: primitiveElement.value?.highlightFirstItem,
      highlightSelected: primitiveElement.value?.highlightSelected
    });
    provideComboboxRootContext({
      modelValue,
      multiple,
      disabled,
      open,
      onOpenChange,
      contentId: "",
      isUserInputted,
      isVirtual,
      inputElement,
      highlightedElement,
      onInputElementChange: (val) => inputElement.value = val,
      triggerElement,
      onTriggerElementChange: (val) => triggerElement.value = val,
      parentElement,
      resetSearchTermOnSelect,
      onResetSearchTerm: resetSearchTerm.on,
      allItems,
      allGroups,
      filterSearch,
      filterState,
      ignoreFilter
    });
    return (_ctx, _cache) => {
      return vue.openBlock(), vue.createBlock(vue.unref(Popper_PopperRoot._sfc_main), null, {
        default: vue.withCtx(() => [
          vue.createVNode(vue.unref(Listbox_ListboxRoot._sfc_main), vue.mergeProps({
            ref_key: "primitiveElement",
            ref: primitiveElement
          }, _ctx.$attrs, {
            modelValue: vue.unref(modelValue),
            "onUpdate:modelValue": _cache[0] || (_cache[0] = ($event) => vue.isRef(modelValue) ? modelValue.value = $event : null),
            style: {
              pointerEvents: vue.unref(open) ? "auto" : void 0
            },
            as: _ctx.as,
            "as-child": _ctx.asChild,
            dir: vue.unref(dir),
            multiple: vue.unref(multiple),
            name: _ctx.name,
            required: _ctx.required,
            disabled: vue.unref(disabled),
            "highlight-on-hover": true,
            by: props.by,
            onHighlight: _cache[1] || (_cache[1] = ($event) => emits("highlight", $event))
          }), {
            default: vue.withCtx(() => [
              vue.renderSlot(_ctx.$slots, "default", {
                open: vue.unref(open),
                modelValue: vue.unref(modelValue)
              })
            ]),
            _: 3
          }, 16, ["modelValue", "style", "as", "as-child", "dir", "multiple", "name", "required", "disabled", "by"])
        ]),
        _: 3
      });
    };
  }
});

exports._sfc_main = _sfc_main;
exports.injectComboboxRootContext = injectComboboxRootContext;
//# sourceMappingURL=ComboboxRoot.cjs.map
