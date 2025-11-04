<template>
  <div class="main-content">
    <!-- Bottom Section -->
    <section class="bottom-section">
      <!-- Game Bookings -->
      <div class="bookings-section">
        <div class="section-header">
          <h2>Programme à venir</h2>
          <div class="search-bar">
            <input type="text" placeholder="🔍 Réchercher un programme" />
            <a href="#" class="see-more">Voir plus ▶</a>
          </div>
        </div>

        
      </div>

      <!-- Golf Course Map -->
      <div class="course-section">
        <div class="course-header">
          <h2>Profile ▼</h2>
          <button class="expand-btn">⛶</button>
        </div>
        
        <div class="course-map">
          <svg viewBox="0 0 300 400" class="course-svg">
            <!-- Golf course background -->
            <defs>
              <pattern id="grass" patternUnits="userSpaceOnUse" width="4" height="4">
                <rect width="4" height="4" fill="#4ade80"/>
                <circle cx="2" cy="2" r="0.5" fill="#22c55e"/>
              </pattern>
            </defs>
            <rect width="300" height="400" fill="url(#grass)" rx="20"/>
            
            <!-- Holes -->
            <circle cx="50" cy="80" r="8" fill="#065f46" stroke="#fff" stroke-width="2"/>
            <circle cx="150" cy="120" r="8" fill="#065f46" stroke="#fff" stroke-width="2"/>
            <circle cx="250" cy="100" r="8" fill="#065f46" stroke="#fff" stroke-width="2"/>
            <circle cx="80" cy="180" r="8" fill="#065f46" stroke="#fff" stroke-width="2"/>
            <circle cx="220" cy="200" r="8" fill="#065f46" stroke="#fff" stroke-width="2"/>
            <circle cx="120" cy="280" r="8" fill="#065f46" stroke="#fff" stroke-width="2"/>
            <circle cx="200" cy="320" r="8" fill="#065f46" stroke="#fff" stroke-width="2"/>
            
            <!-- Sand traps -->
            <ellipse cx="100" cy="150" rx="15" ry="10" fill="#fbbf24"/>
            <ellipse cx="180" cy="250" rx="12" ry="8" fill="#fbbf24"/>
            
            <!-- Water hazard -->
            <ellipse cx="150" cy="200" rx="25" ry="15" fill="#3b82f6"/>
            
            <!-- Flag -->
            <circle cx="150" cy="320" r="12" fill="#ef4444"/>
            <text x="150" y="325" text-anchor="middle" fill="white" font-size="10">🏁</text>
          </svg>
        </div>

      </div>
    </section>
  </div>
</template>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.main-content {
  max-width: 1400px;
  margin: 0 auto;
  padding: 2rem;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Bottom Section */
.bottom-section {
  display: grid;
  grid-template-columns: 1fr 400px;
  gap: 2rem;
}

/* Bookings Section */
.bookings-section {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.section-header h2 {
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
}

.search-bar {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.search-bar input {
  background: #f3f4f6;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-size: 14px;
  width: 200px;
}

.see-more {
  color: #6b7280;
  text-decoration: none;
  font-size: 14px;
}

.month-header {
  font-size: 12px;
  font-weight: bold;
  color: #6b7280;
  margin: 1.5rem 0 1rem 0;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.booking-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-radius: 12px;
  margin-bottom: 0.5rem;
  transition: all 0.2s;
}

.booking-item:hover {
  background: #f9fafb;
}

.booking-item.highlighted {
  background: #fef3c7;
  border: 1px solid #f59e0b;
}

.booking-date {
  text-align: center;
  min-width: 60px;
}

.date-day {
  font-size: 12px;
  color: #6b7280;
  text-transform: uppercase;
}

.date-number {
  font-size: 24px;
  font-weight: bold;
  color: #1f2937;
}

.booking-details {
  flex: 1;
}

.booking-time,
.booking-location {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 0.25rem;
}

.booking-info {
  flex: 2;
}

.team-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.25rem;
}

.player-avatar {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  object-fit: cover;
}

.team-name {
  font-weight: 500;
  color: #1f2937;
}

.team-subtitle {
  font-size: 12px;
  color: #6b7280;
}

.status-badge {
  font-size: 10px;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-weight: bold;
  text-transform: uppercase;
}

.status-badge.paid {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.unpaid {
  background: #fee2e2;
  color: #991b1b;
}

.team-count {
  font-size: 12px;
  color: #6b7280;
}

.booking-status {
  font-size: 12px;
  color: #6b7280;
}

.booking-actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  background: none;
  border: 1px solid #d1d5db;
  width: 32px;
  height: 32px;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #f3f4f6;
}

.accept-btn {
  background: #ef4444;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.accept-btn:hover {
  background: #dc2626;
}

/* Course Section */
.course-section {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.course-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.course-header h2 {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.expand-btn {
  background: none;
  border: none;
  font-size: 16px;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 6px;
  transition: background 0.2s;
}

.expand-btn:hover {
  background: #f3f4f6;
}

.course-map {
  margin-bottom: 1.5rem;
}

.course-svg {
  width: 100%;
  height: 200px;
  border-radius: 12px;
}

.course-stats {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.stat-label {
  font-size: 14px;
  color: #6b7280;
}

.stat-value {
  font-size: 18px;
  font-weight: bold;
  color: #1f2937;
}

.stat-unit {
  font-size: 12px;
  font-weight: normal;
  color: #6b7280;
}

.player-profile {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 12px;
}

.profile-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.profile-info {
  flex: 1;
}

.profile-name {
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.profile-status {
  font-size: 12px;
  color: #6b7280;
}

.profile-stats {
  text-align: right;
}

.profile-score {
  font-size: 18px;
  font-weight: bold;
  color: #1f2937;
  margin-bottom: 0.25rem;
}

.profile-details {
  font-size: 12px;
  color: #6b7280;
}

.profile-details span {
  color: #1f2937;
  font-weight: 500;
}

/* Responsive */
@media (max-width: 1024px) {
  .bottom-section {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  
  .course-section {
    order: -1;
  }
}

@media (max-width: 768px) {
  .main-content {
    padding: 1rem;
  }
  
  .section-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .search-bar {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .search-bar input {
    width: 100%;
  }
  
  .booking-item {
    flex-direction: column;
    gap: 0.75rem;
    align-items: stretch;
  }
  
  .booking-date {
    align-self: flex-start;
    min-width: auto;
  }
  
  .booking-actions {
    align-self: flex-end;
  }
  
  .team-info {
    flex-wrap: wrap;
  }
  
  .course-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }
  
  .player-profile {
    flex-direction: column;
    text-align: center;
    gap: 0.75rem;
  }
  
  .profile-stats {
    text-align: center;
  }
}

@media (max-width: 480px) {
  .booking-item {
    padding: 0.75rem;
  }
  
  .team-info {
    gap: 0.25rem;
  }
  
  .status-badge {
    font-size: 9px;
    padding: 0.2rem 0.4rem;
  }
  
  .course-stats {
    grid-template-columns: 1fr;
  }
  
  .stat-value {
    font-size: 16px;
  }
}
</style>
