fetch('budget_api/fetch_budget_progress.php')
    .then(response => response.text()) // Đổi tạm sang text để kiểm tra
    .then(text => {
        try {
            const data = JSON.parse(text); // Thử parse JSON
            if (data.status === 'success') {
                const budgets = data.data;

                budgets.forEach((budget, index) => {
                    const totalBudget = parseFloat(budget.total_budget);
                    const totalExpense = parseFloat(budget.total_expense);
                    const remainingBudget = parseFloat(budget.remaining_budget);

                    if (isNaN(totalBudget) || isNaN(totalExpense) || isNaN(remainingBudget)) {
                        console.error('Invalid data for budget:', budget);
                        return;
                    }

                    // Tính số ngày còn lại
                    const startDate = new Date(budget.start_date);
                    const endDate = new Date(budget.end_date);
                    const today = new Date();
                    const daysLeft = Math.max(
                        Math.ceil((endDate - today) / (1000 * 60 * 60 * 24)),
                        0
                    );

                    const containerWidth = document.getElementById("remaining-budget-chart").offsetWidth;
                    const width = containerWidth * 0.9;
                    const height = 30;

                    // Container chính cho mỗi category
                    const container = d3
                        .select("#remaining-budget-chart")
                        .append("div")
                        .style("margin-bottom", "0px");

                    // Đường thẳng ngăn cách
                    if (index !== 0) {
                        container.append("hr")
                            .style("border", "1px solid #ccc")
                            .style("margin", "20px 0");
                    }

                    // Tên category
                    container.append("div")
                        .style("font-weight", "bold")
                        .style("font-size", "24px")
                        .style("color", "#333")
                        .style("text-align", "center")
                        .style("margin-bottom", "0px")
                        .text(`${budget.category_name} (${budget.start_date} - ${budget.end_date})`);

                    // Tạo SVG container
                    const svg = container
                        .append("svg")
                        .attr("width", width)
                        .attr("height", height + 80);

                    const defs = svg.append("defs");
                    const gradient = defs.append("linearGradient")
                        .attr("id", `gradient-spent-${budget.budget_id}`)
                        .attr("x1", "0%")
                        .attr("y1", "0%")
                        .attr("x2", "100%")
                        .attr("y2", "0%");
                    gradient.append("stop")
                        .attr("offset", "0%")
                        .attr("stop-color", "#56CCF2");
                    gradient.append("stop")
                        .attr("offset", "100%")
                        .attr("stop-color", "#2F80ED");

                    svg
                        .append("rect")
                        .attr("x", 0)
                        .attr("y", 20)
                        .attr("width", width)
                        .attr("height", height)
                        .attr("fill", "#E5E5E5")
                        .attr("rx", height / 2);

                    svg
                        .append("rect")
                        .attr("x", 0)
                        .attr("y", 20)
                        .attr("width", (totalExpense / totalBudget) * width)
                        .attr("height", height)
                        .attr("fill", `url(#gradient-spent-${budget.budget_id})`)
                        .attr("rx", height / 2);

                    svg
                        .append("circle")
                        .attr("cx", Math.min(Math.max((totalExpense / totalBudget) * width, 15), width - 15))
                        .attr("cy", 20 + height / 2)
                        .attr("r", 10)
                        .attr("fill", "#2F80ED")
                        .attr("stroke", "#fff")
                        .attr("stroke-width", 2);

                    svg
                        .append("text")
                        .attr("x", Math.min(Math.max((totalExpense / totalBudget) * width, 30), width - 30))
                        .attr("y", 15)
                        .attr("text-anchor", "middle")
                        .attr("font-size", "12px")
                        .attr("font-weight", "600")
                        .attr("fill", "#666")
                        .text("Today");

                    svg
                        .append("text")
                        .attr("x", width / 2)
                        .attr("y", height + 50)
                        .attr("text-anchor", "middle")
                        .attr("font-size", "18px")
                        .attr("font-weight", "bold")
                        .attr("fill", "#333")
                        .text(`$${remainingBudget.toFixed(2)} remaining`);

                    const footerData = [
                        { label: "Total budgets", value: `$${(totalBudget / 1000).toFixed(1)}K` },
                        { label: "Total spent", value: `$${(totalExpense / 1000).toFixed(1)}K` },
                        { label: "Days left", value: `${daysLeft} days` },
                    ];

                    const footer = container
                        .append("div")
                        .style("display", "flex")
                        .style("justify-content", "space-between");

                    footer
                        .selectAll("div")
                        .data(footerData)
                        .enter()
                        .append("div")
                        .style("text-align", "center")
                        .html(
                            (d) => `
                                <div style="font-weight: bold; font-size: 16px;">${d.value}</div>
                                <div style="color: #666; font-size: 14px;">${d.label}</div>
                            `
                        );
                });
            } else {
                console.error('API error message:', data.message);
            }
        } catch (error) {
            console.error('Response is not valid JSON:', text); // Ghi log toàn bộ phản hồi
        }
    })
    .catch(error => console.error('Error fetching data:', error));
