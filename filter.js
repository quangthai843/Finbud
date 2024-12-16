const app = Vue.createApp({
    data() {
        return {
            selectedMonth: new Date().toISOString().slice(0, 7), // Tháng hiện tại
            expenses: [],
            categories: [],
            subCategories: [],
            currentPage: 1, // Trang hiện tại
            itemsPerPage: 5, // Số dòng trên mỗi trang
            filters: {
                minAmount: '',
                maxAmount: '', // Giá trị lớn mặc định
                category: '',
                subCategory: '',
                startDate: '',
                endDate: '',
            },
        };
    },
    computed: {
        filteredExpenses() {
            if (
                !this.filters.minAmount &&
                !this.filters.maxAmount &&
                !this.filters.category &&
                !this.filters.subCategory &&
                !this.filters.startDate &&
                !this.filters.endDate
            ) {
                return []; // Không hiển thị kết quả nếu không có bộ lọc
            }
            
            return this.expenses.filter(expense => {
                const minAmount = this.filters.minAmount || 0;
                const maxAmount = this.filters.maxAmount || Number.MAX_VALUE;
                const category = this.filters.category;
                const subCategory = this.filters.subCategory;
                const startDate = this.filters.startDate;
                const endDate = this.filters.endDate;
                
                const matchesAmount = expense.amount >= minAmount && expense.amount <= maxAmount;
                const matchesCategory = !category || expense.category_name == category;
                const matchesSubCategory = !subCategory || expense.sub_category_name == subCategory;
                const matchesStartDate = !startDate || new Date(expense.expense_date) >= new Date(startDate);
                const matchesEndDate = !endDate || new Date(expense.expense_date) <= new Date(endDate);
                
                return matchesAmount && matchesCategory && matchesSubCategory && matchesStartDate && matchesEndDate;
            });
        },
        paginatedExpenses() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredExpenses.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.filteredExpenses.length / this.itemsPerPage);
        },
    },
    methods: {
        async fetchExpenseData() {
            try {
                const response = await fetch(`expense_api/fetch_expense_category_percent.php?month=${this.selectedMonth}`);
                const result = await response.json();

                if (result.status === 'success') {
                    this.expenses = result.data; // Cập nhật dữ liệu biểu đồ
                    renderPieChart(this.expenses); // Gọi hàm vẽ biểu đồ với dữ liệu mới
                } else {
                    console.error('Error:', result.message);
                }
            } catch (error) {
                console.error('Error fetching expense data:', error);
            }
        },
        async fetchExpenses() {
            try {
                const response = await fetch('fetch_expenses.php');
                const data = await response.json();
                console.log("Expenses fetched:", data); // In dữ liệu expenses
                this.expenses = data; // Lưu danh sách transaction
            } catch (error) {
                console.error("Error fetching expenses:", error);
            }
        },
        async fetchCategories() {
            try {
                const response = await fetch('expense_api/filter1.php');
                if (!response.ok) throw new Error('Failed to fetch categories');
                const data = await response.json();
                this.categories = data || []; // Đảm bảo luôn có giá trị mảng
            } catch (error) {
                console.error('Error fetching categories:', error);
                this.categories = []; // Reset thành mảng rỗng nếu có lỗi
            }
        },
        
        async fetchSubCategories(categoryName) {
            try {
                if (!categoryName) {
                    this.subCategories = [];
                    return;
                }
                const categoryId = this.categories.find(cat => cat.category_name === categoryName)?.category_id;
                if (!categoryId) {
                    console.error("Category ID not found for category name:", categoryName);
                    return;
                }
                const response = await fetch(`expense_api/filter2.php?category_id=${categoryId}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                this.subCategories = data || []; // Đảm bảo dữ liệu không null
            } catch (error) {
                console.error("Error fetching sub-categories:", error);
                this.subCategories = []; // Reset sub-categories nếu lỗi
            }
        },
        changePage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
            }
        },
    },
    
    watch: {
        'filters.category': function (newValue) {
            this.filters.subCategory = ''; // Reset sub-category khi thay đổi category
            this.fetchSubCategories(newValue); // Fetch sub-categories dựa trên category mới
        },
    },
    async mounted() {
        await this.fetchCategories();
        await this.fetchExpenses();
    },
});

app.mount('#app');
