
// Khởi tạo ngày hiện tại
let currentDate = new Date();
const monthNames = [
    "January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
];

// Cập nhật tháng hiển thị
function updateMonthDisplay() {
    const currentMonthElement = document.getElementById("current-month");
    const formattedMonth = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    currentMonthElement.innerText = formattedMonth;
    renderPieChart(currentDate.toISOString().slice(0, 7)); // Cập nhật biểu đồ với tháng hiện tại
}

async function renderPieChart(selectedMonth = new Date().toISOString().slice(0, 7)) {
    try {
        // Fetch data từ API với tham số tháng
        const response = await fetch(`expense_api/fetch_expense_category_percent.php?month=${selectedMonth}`);
        const result = await response.json();

        // Xóa biểu đồ cũ
        d3.select('#pie-chart').selectAll('*').remove();

        if (result.status === 'success') {
            const data = result.data;
            if (!data || data.length === 0) {
                // Hiển thị thông báo nếu không có dữ liệu
                d3.select('#pie-chart')
                    .append('div')
                    .text('No expense transaction for this month');
                return;
            }

            // Kiểm tra và chuyển đổi dữ liệu
            data.forEach(d => {
                d.total_amount = parseFloat(d.total_amount) || 0; // Chuyển đổi total_amount thành số
            });

            // Thiết lập SVG
            const width = 400;
            const height = 400;
            const radius = Math.min(width, height) / 2;

            const svg = d3
                .select('#pie-chart')
                .append('svg')
                .attr('width', width + 200) // Tăng chiều rộng để đủ chỗ cho legend
                .attr('height', height)
                .append('g')
                .attr('transform', `translate(${width / 2}, ${height / 2})`);

            // Tạo scale màu
            const color = d3.scaleOrdinal(d3.schemeCategory10);

            // Tạo Pie và Arc
            const pie = d3
                .pie()
                .value(d => d.total_amount)
                .sort(null);

            const arc = d3
                .arc()
                .innerRadius(0) // Biểu đồ tròn
                .outerRadius(radius);

            // Thêm tooltip
            const tooltip = d3.select('body')
                .append('div')
                .style('position', 'absolute')
                .style('background', '#fff')
                .style('border', '1px solid #ccc')
                .style('padding', '5px 10px')
                .style('border-radius', '5px')
                .style('box-shadow', '0px 2px 5px rgba(0, 0, 0, 0.3)')
                .style('visibility', 'hidden')
                .style('font-size', '12px');

            // Vẽ biểu đồ
            svg.selectAll('path')
                .data(pie(data))
                .enter()
                .append('path')
                .attr('d', arc)
                .attr('fill', d => color(d.data.category_name))
                .attr('stroke', '#fff')
                .attr('stroke-width', '2px')
                .on('mouseover', (event, d) => {
                    // Hiển thị tooltip
                    tooltip.style('visibility', 'visible')
                        .html(`<strong>${d.data.category_name}</strong>: $${d.data.total_amount.toFixed(2)}`);
                })
                .on('mousemove', event => {
                    // Cập nhật vị trí tooltip
                    tooltip.style('top', `${event.pageY - 30}px`)
                        .style('left', `${event.pageX + 10}px`);
                })
                .on('mouseout', () => {
                    // Ẩn tooltip khi hover kết thúc
                    tooltip.style('visibility', 'hidden');
                });

            // Thêm nhãn vào trong biểu đồ
            svg.selectAll('text')
                .data(pie(data))
                .enter()
                .append('text')
                .text(d => `${d.data.percentage}%`)
                .attr('transform', d => `translate(${arc.centroid(d)})`)
                .style('text-anchor', 'middle')
                .style('font-size', '12px');

            // Tạo legend (chú giải)
            const legend = d3.select('#pie-chart svg')
                .append('g')
                .attr('transform', `translate(${width}, 20)`); // Đặt legend bên phải biểu đồ

            legend.selectAll('rect')
                .data(data)
                .enter()
                .append('rect')
                .attr('x', 10)
                .attr('y', (d, i) => i * 20)
                .attr('width', 10)
                .attr('height', 10)
                .attr('fill', d => color(d.category_name));

            legend.selectAll('text')
                .data(data)
                .enter()
                .append('text')
                .attr('x', 25)
                .attr('y', (d, i) => i * 20 + 10)
                .text(d => d.category_name)
                .style('font-size', '12px')

                .attr('alignment-baseline', 'middle');


        } else {
            console.error('Failed to fetch data:', result.message);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Sự kiện khi nhấn nút Previous Month
document.getElementById("prev-month").addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() - 1); // Giảm 1 tháng
    updateMonthDisplay();
});

// Sự kiện khi nhấn nút Next Month
document.getElementById("next-month").addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() + 1); // Tăng 1 tháng
    updateMonthDisplay();
});

// Gọi hàm để hiển thị tháng khi trang tải
updateMonthDisplay();
