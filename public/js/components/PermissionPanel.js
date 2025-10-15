/**
 * PermissionPanel Component
 *
 * Replaces jQuery slideToggle functionality for permission panels
 * Provides smooth Vue transitions for showing/hiding panel content
 *
 * Usage:
 * <permission-panel
 *   :panel-id="panelId"
 *   :title="title"
 *   :company-name="companyName"
 * >
 *   <template #content>
 *     <!-- Panel content (form, etc.) -->
 *   </template>
 * </permission-panel>
 */

const { ref } = Vue;

export default {
	name: 'PermissionPanel',
	props: {
		panelId: {
			type: String,
			required: true
		},
		title: {
			type: String,
			required: true
		},
		companyName: {
			type: String,
			required: true
		},
		initiallyOpen: {
			type: Boolean,
			default: false
		}
	},
	setup(props) {
		const isOpen = ref(props.initiallyOpen);

		const toggle = () => {
			isOpen.value = !isOpen.value;
			console.log(`Panel ${props.panelId} toggled:`, isOpen.value ? 'open' : 'closed');
		};

		return {
			isOpen,
			toggle
		};
	},
	template: `
		<div class="row flex-column align-items-center">
			<div class="col-lg-9 bg-body-secondary rounded mt-3 p-2">
				<span class="d-flex justify-content-between">
					<h2 class="mx-1 cursor-pointer" @click="toggle" style="cursor: pointer;">
						{{ title }} в <b>{{ companyName }}</b>
					</h2>
					<button class="lead m-1 btn btn-link" type="button" @click="toggle">
						<i class="bi bi-nintendo-switch"></i>
					</button>
				</span>

				<!-- Vue Transition for smooth slide effect -->
				<transition
					name="slide"
					@enter="onEnter"
					@leave="onLeave"
				>
					<div v-show="isOpen" :id="panelId" class="perm-panel bg-body-tertiary rounded p-3">
						<slot name="content"></slot>
					</div>
				</transition>
			</div>
		</div>
	`,
	methods: {
		// Custom transition hooks for slide effect
		onEnter(el) {
			el.style.height = '0';
			el.style.overflow = 'hidden';
			el.style.transition = 'height 0.3s ease-out';

			// Force a reflow to ensure the transition works
			el.offsetHeight;

			el.style.height = el.scrollHeight + 'px';

			// After transition ends, set height to auto for responsive content
			setTimeout(() => {
				el.style.height = 'auto';
				el.style.overflow = 'visible';
			}, 300);
		},
		onLeave(el) {
			el.style.height = el.scrollHeight + 'px';
			el.style.overflow = 'hidden';
			el.style.transition = 'height 0.3s ease-in';

			// Force a reflow
			el.offsetHeight;

			el.style.height = '0';
		}
	}
};
