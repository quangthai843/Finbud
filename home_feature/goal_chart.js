fetch('fetch_finance_goals.php')
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const financialGoals = data.data;

            // Xóa nội dung cũ trước khi thêm mới (nếu cần thiết)
            d3.select("#financial-goals-chart").html("");

            financialGoals.forEach((goal) => {
                const percentage = Math.min(Math.max(goal.current_amount / goal.target_amount, 0), 1);

                // Tạo container cho mỗi mục tiêu
                const container = d3
                    .select("#financial-goals-chart")
                    .append("div")
                    .attr("class", "goal-container");

                const width = 200;
                const height = 200;
                const radius = Math.min(width, height) / 2 - 10;

                // Tạo SVG
                const svg = container
                    .append("svg")
                    .attr("width", width)
                    .attr("height", height)
                    .append("g")
                    .attr("transform", `translate(${width / 2}, ${height / 2})`);

                // Tạo defs cho gradient
                const defs = svg.append("defs");
                const gradientId = `gradient-${goal.goal_name.replace(/\s+/g, "-")}`;
                const gradient = defs.append("linearGradient")
                    .attr("id", gradientId)
                    .attr("x1", "0%")
                    .attr("y1", "0%")
                    .attr("x2", "100%")
                    .attr("y2", "0%");

                // Màu nhạt ở đầu
                gradient.append("stop")
                    .attr("offset", "0%")
                    .attr("stop-color", "#B3E5FC");

                // Màu đậm ở cuối
                gradient.append("stop")
                    .attr("offset", "100%")
                    .attr("stop-color", "#0288D1");

                // Tạo vòng tròn nền (background)
                const backgroundArc = d3.arc()
                    .innerRadius(radius - 15)
                    .outerRadius(radius)
                    .startAngle(0)
                    .endAngle(2 * Math.PI);

                // Tạo vòng tròn foreground (phần đã hoàn thành)
                const foregroundArc = d3.arc()
                    .innerRadius(radius - 15)
                    .outerRadius(radius)
                    .startAngle(0)
                    .endAngle(2 * Math.PI * percentage);

                // Thêm phần nền
                svg.append("path")
                    .attr("d", backgroundArc())
                    .attr("fill", "#E0E0E0");

                // Thêm phần đã hoàn thành với gradient
                svg.append("path")
                    .attr("d", foregroundArc())
                    .attr("fill", `url(#${gradientId})`);

                // Thêm phần trăm và tên mục tiêu vào giữa hình tròn
                svg.append("text")
                    .attr("text-anchor", "middle")
                    .attr("dy", "-0.5em")
                    .style("font-size", "20px")
                    .style("font-weight", "bold")
                    .style("fill", "#0288D1")
                    .text(`${(percentage * 100).toFixed(1)}%`);

                svg.append("text")
                    .attr("text-anchor", "middle")
                    .attr("dy", "1em")
                    .style("font-size", "12px")
                    .style("fill", "#666")
                    .text(goal.goal_name);

                // Thêm chi tiết mục tiêu bên dưới hình tròn
                container
                    .append("div")
                    .attr("class", "goal-details")
                    .html(`
                        <div><i class="fas fa-dollar-sign"></i> Target: $${goal.target_amount.toLocaleString()}</div>
                        <div><i class="fas fa-calendar-alt"></i> By ${goal.target_date}</div>
                    `);
            });
        } else {
            console.error('Error fetching financial goals:', data.message);
        }
    })
    .catch(error => console.error('Error fetching financial goals:', error));
