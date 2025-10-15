/**
 * XlsxReportForm Component
 *
 * Replaces jQuery percentage calculation logic for XLSX report forms
 * Provides reactive percentage calculations using Vue computed properties
 *
 * Functionality:
 * 1. Calculates percentages for each worker based on their sold amount
 * 2. Toggles "required" attribute on inputs based on checkbox state
 * 3. Reactive updates when any input changes
 *
 * Usage in Blade:
 * <xlsx-report-form
 *   :workers='@json($workers)'
 *   :company-id="{{ $data->company->id }}"
 *   :old-values='@json(old())'
 * ></xlsx-report-form>
 */

const { ref, computed, watch, onMounted } = Vue;

export default {
	name: 'XlsxReportForm',
	props: {
		workers: {
			type: Array,
			required: true
		},
		companyId: {
			type: [Number, String],
			required: true
		},
		oldValues: {
			type: Object,
			default: () => ({})
		}
	},
	setup(props) {
		// Form field refs
		const forDate = ref(props.oldValues.for_date || new Date().toISOString().split('T')[0]);
		const fileInput = ref(null);
		const note = ref(props.oldValues.note || '');
		const repostCheckbox = ref(false);

		// Worker sold amounts (reactive)
		const workerSales = ref({});

		// Initialize worker sales from old values or default to 0
		onMounted(() => {
			props.workers.forEach(worker => {
				const oldValue = props.oldValues[`worker_sold_${worker.id}`];
				workerSales.value[worker.id] = parseFloat(oldValue) || 0;
			});
			console.log('XlsxReportForm mounted with', props.workers.length, 'workers');
		});

		// Computed: total of all worker sales
		const totalSales = computed(() => {
			return Object.values(workerSales.value).reduce((sum, val) => sum + (parseFloat(val) || 0), 0);
		});

		// Computed: percentage for each worker
		const workerPercentages = computed(() => {
			const percentages = {};
			const total = totalSales.value;

			if (total === 0) {
				// If total is 0, all percentages are 0%
				props.workers.forEach(worker => {
					percentages[worker.id] = '0.0 %';
				});
				return percentages;
			}

			props.workers.forEach(worker => {
				const amount = parseFloat(workerSales.value[worker.id]) || 0;
				const percent = ((amount / total) * 100).toFixed(1);
				percentages[worker.id] = `${percent} %`;
			});

			return percentages;
		});

		// Watch checkbox to toggle required attribute
		watch(repostCheckbox, (isChecked) => {
			// When checkbox is checked, inputs are NOT required
			// When checkbox is unchecked, inputs ARE required
			console.log('Checkbox changed:', isChecked ? 'checked (not required)' : 'unchecked (required)');
		});

		return {
			forDate,
			fileInput,
			note,
			repostCheckbox,
			workerSales,
			totalSales,
			workerPercentages
		};
	},
	template: `
		<form :action="'/company/' + companyId + '/mod/report_xlsx'" method="post" enctype="multipart/form-data">
			<input type="hidden" name="_token" :value="window.csrfToken()">

			<div class="form-group mb-2">
				<label for="for_date">Дата:</label>
				<input
					type="date"
					class="form-control"
					id="for_date"
					name="for_date"
					v-model="forDate"
					required
				>
			</div>

			<div class="form-group mb-2">
				<label for="file">Файл отчёта:</label>
				<input
					type="file"
					class="form-control"
					name="file"
					:required="!repostCheckbox"
					ref="fileInput"
				>
			</div>

			<div class="my-2"></div>

			<div class="form-group mb-2 d-none">
				<label for="note">Заметка:</label>
				<textarea
					name="note"
					class="form-control w-100"
					placeholder="Написать заметки"
					style="height: 100px"
					v-model="note"
				></textarea>
			</div>

			<label>Продажи:</label>
			<div v-for="worker in workers" :key="worker.id">
				<input type="hidden" :name="'worker_name_' + worker.id" :value="worker.full_name">
				<div class="input-group mb-2">
					<span class="input-group-text col-8">
						{{ worker.full_name }}
						<small class="ms-2 text-muted">{{ workerPercentages[worker.id] }}</small>
					</span>
					<input
						type="number"
						class="form-control col-4"
						:name="'worker_sold_' + worker.id"
						placeholder="Sold"
						v-model.number="workerSales[worker.id]"
						:required="!repostCheckbox"
						aria-label="Sold"
					>
				</div>
			</div>

			<div class="d-flex justify-content-center">
				<button type="submit" class="btn btn-primary">Отправить</button>
			</div>
		</form>
	`
};
