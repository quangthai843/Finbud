async function renderBarChart(type = 'week') {
    try {
        // Gọi API để lấy dữ liệu
        const response = await fetch(`expense_api/fetch_expense_comparison.php?type=${type}`);
        const result = await response.json();

        if (result.status === 'success') {
            const chartData = [
                { label: 'Last', value: result.data.last },
                { label: 'This', value: result.data.current },
            ];

            // Xóa biểu đồ cũ
            d3.select('#bar-chart').selectAll('*').remove();

            const width = 300;
            const height = 300;
            const margin = { top: 20, right: 20, bottom: 40, left: 40 };

            const svg = d3
                .select('#bar-chart')
                .append('svg')
                .attr('width', width + margin.left + margin.right)
                .attr('height', height + margin.top + margin.bottom)
                .append('g')
                .attr('transform', `translate(${margin.left}, ${margin.top})`);

            // Tạo scale cho trục X và Y
            const x = d3
                .scaleBand()
                .domain(chartData.map(d => d.label))
                .range([0, width])
                .padding(0.3);

            const y = d3
                .scaleLinear()
                .domain([0, d3.max(chartData, d => d.value)])
                .nice()
                .range([height, 0]);

            // Vẽ trục X
            svg
                .append('g')
                .attr('transform', `translate(0, ${height})`)
                .call(d3.axisBottom(x))
                .selectAll('text')
                .attr('text-anchor', 'middle')
                .style('font-family', 'Poppins, sans-serif');

            // Vẽ trục Y
            svg
                .append('g')
                .call(d3.axisLeft(y).ticks(5))
                .selectAll('text')
                .style('font-family', 'Poppins, sans-serif');

            // Vẽ các cột (bars)
            svg
                .selectAll('.bar')
                .data(chartData)
                .enter()
                .append('rect')
                .attr('x', d => x(d.label))
                .attr('y', d => y(d.value))
                .attr('width', x.bandwidth())
                .attr('height', d => height - y(d.value))
                .attr('fill', '#007bff')
                .attr('rx', 5); // Bo góc

            // Thêm giá trị trên cột
            svg
                .selectAll('.text')
                .data(chartData)
                .enter()
                .append('text')
                .attr('x', d => x(d.label) + x.bandwidth() / 2)
                .attr('y', d => y(d.value) - 5)
                .attr('text-anchor', 'middle')
                .text(d => `$${d.value.toFixed(2)}`)
                .style('font-family', 'Poppins, sans-serif')
                .style('font-size', '12px');

            // Cập nhật header
            document.getElementById('total-spent').innerText = `$${chartData[1].value.toFixed(2)}`;
            document.getElementById('comparison-period').innerText = `this ${type}`;
        } else {
            console.error('Failed to fetch data:', result.message);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Sự kiện chuyển đổi giữa tuần và tháng
document.getElementById('week-btn').addEventListener('click', () => {
    document.getElementById('week-btn').classList.add('active');
    document.getElementById('month-btn').classList.remove('active');
    renderBarChart('week');
});

document.getElementById('month-btn').addEventListener('click', () => {
    document.getElementById('month-btn').classList.add('active');
    document.getElementById('week-btn').classList.remove('active');
    renderBarChart('month');
});

// Hiển thị biểu đồ ban đầu
renderBarChart('week');
