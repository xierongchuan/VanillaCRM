/**
 * DepartmentPostSelector Component
 *
 * Replaces jQuery AJAX with Vue fetch API for dynamic department/post selection
 * Loads posts based on selected department
 */

const DepartmentPostSelector = {
  name: 'DepartmentPostSelector',
  props: {
    companyId: {
      type: [Number, String],
      required: true
    },
    departments: {
      type: Array,
      required: true
    },
    selectedDepartmentId: {
      type: [Number, String],
      default: null
    },
    selectedPostId: {
      type: [Number, String],
      default: null
    },
    departmentName: {
      type: String,
      default: 'department'
    },
    postName: {
      type: String,
      default: 'post'
    },
    showPost: {
      type: Boolean,
      default: true
    },
    postRequired: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      currentDepartmentId: this.selectedDepartmentId,
      currentPostId: this.selectedPostId,
      posts: [],
      isLoadingPosts: false,
      error: null
    };
  },
  computed: {
    departmentFieldId() {
      return `worker_department_${this.companyId}`;
    },
    postFieldId() {
      return `worker_post_${this.companyId}`;
    }
  },
  watch: {
    async currentDepartmentId(newDepId) {
      if (newDepId && this.showPost) {
        await this.loadPosts(newDepId);
      } else {
        this.posts = [];
        this.currentPostId = null;
      }
    }
  },
  async mounted() {
    // Load posts for initial department if set
    if (this.currentDepartmentId && this.showPost) {
      await this.loadPosts(this.currentDepartmentId);
    }

    console.log('DepartmentPostSelector mounted for company', this.companyId);
  },
  methods: {
    async loadPosts(departmentId) {
      if (!departmentId) {
        this.posts = [];
        return;
      }

      this.isLoadingPosts = true;
      this.error = null;

      try {
        const url = `/company/${this.companyId}/department/${departmentId}/posts`;

        // Use apiFetch helper (from helpers.js)
        const data = await apiFetch(url, {
          method: 'POST'
        });

        this.posts = data || [];

        // Set the selected post if it exists in the loaded posts
        if (this.selectedPostId) {
          const postExists = this.posts.find(p => p.id == this.selectedPostId);
          if (postExists) {
            this.currentPostId = this.selectedPostId;
          } else {
            this.currentPostId = null;
          }
        } else {
          this.currentPostId = null;
        }

        console.log(`Loaded ${this.posts.length} posts for department ${departmentId}`);
      } catch (err) {
        console.error('Error loading posts:', err);
        this.error = 'Failed to load posts. Please try again.';
        this.posts = [];
        this.currentPostId = null;
      } finally {
        this.isLoadingPosts = false;
      }
    }
  },
  template: `
    <div class="department-post-selector">
      <!-- Department Select -->
      <select
        v-model="currentDepartmentId"
        :id="departmentFieldId"
        :name="departmentName"
        class="form-select form-select-lg mb-3"
        aria-label="Department selection"
        required>
        <option value="" selected>Select Department</option>
        <option
          v-for="dept in departments"
          :key="dept.id"
          :value="dept.id">
          {{ dept.name }}
        </option>
      </select>

      <!-- Post Select (only show if showPost is true) -->
      <div v-if="showPost">
        <select
          v-model="currentPostId"
          :id="postFieldId"
          :name="postName"
          class="form-select form-select-lg mb-3"
          aria-label="Post selection"
          :disabled="!currentDepartmentId || isLoadingPosts"
          :required="postRequired">
          <option value="">
            {{ isLoadingPosts ? 'Loading posts...' : 'Select Post' }}
          </option>
          <option
            v-for="post in posts"
            :key="post.id"
            :value="post.id">
            {{ post.name }}
          </option>
        </select>

        <!-- Loading indicator -->
        <div v-if="isLoadingPosts" class="text-muted small mb-2">
          <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
          Loading posts...
        </div>

        <!-- Error message -->
        <div v-if="error" class="alert alert-danger alert-sm mb-2" role="alert">
          {{ error }}
        </div>
      </div>

      <!-- Hidden input for company_id (for backend reference) -->
      <input type="hidden" id="company_id" :value="companyId">

      <!-- Hidden input for selected post (for update form compatibility) -->
      <input v-if="selectedPostId && showPost" type="hidden" id="worker_post_id" :value="selectedPostId">
    </div>
  `
};

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
  module.exports = DepartmentPostSelector;
}

// Make available globally
window.DepartmentPostSelector = DepartmentPostSelector;
