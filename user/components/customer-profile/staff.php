  <!-- Staff Tab -->
  <div class="tab-content" id="staff">
      <h3 class="section-title">Assigned Staff</h3>

      <!-- Assign Staff Form -->
      <div class="assign-staff-form">
          <h4>Assign New Staff</h4>
          <form action="assigned-staff" method="POST" class="ajax_form reset" data-reset="reset">
              <div style="display: flex; gap: 10px; align-items: end;">
                  <div style="flex: 1;">
                      <label style="display: block; margin-bottom: 5px; font-weight: 500;">Select Staff</label>
                      <select name="staff_id" style="width: 100%; padding: 10px; border: 1px solid #e1e5eb; border-radius: 6px;" required>
                          <option value="">-- Select Staff Member --</option>
                          <?php foreach ($all_staff as $staff): ?>
                              <option value="<?= $staff['id'] ?>">
                                  <?= $staff['title'] . ' ' . $staff['fname'] . ' ' . $staff['lname'] ?>
                                  (<?= $staff['email'] ?>)
                              </option>
                          <?php endforeach; ?>
                      </select>
                  </div>
                  <div>
                      <button type="submit" name="assign_staff" class="btn btn-primary">
                          <input type="hidden" name="customer_id" value="<?= $get_id ?>">
                          <input type="hidden" name="assign_staff" value="true">
                          <i class="fas fa-user-plus"></i> Assign Staff
                      </button>
                  </div>
              </div>
          </form>
      </div>

      <!-- Staff List -->
      <?php if (!empty($assigned_staff)): ?>
          <?php foreach ($assigned_staff as $staff): ?>
              <div class="staff-card">
                  <div class="staff-header">
                      <div class="staff-name">
                          <?= $staff['title'] . ' ' . $staff['fname'] . ' ' . $staff['lname'] ?>
                      </div>
                      <div class="staff-actions">
                          <!-- <a href="mailto:<?= $staff['email'] ?>" class="btn" style="padding: 5px 10px; font-size: 12px;">
                                        <i class="fas fa-envelope"></i> Email
                                    </a> -->
                          <form action="assigned-staff" method="POST" class="ajax_form reset ml-4" data-reset="reset">
                              <input type="hidden" name="assignment_id" value="<?= $staff['id'] ?>">
                              <input type="hidden" name="remove_staff" value="true">
                              <button type="submit" name="remove_staff" class="btn" style="padding: 5px 10px; font-size: 12px; background: #e74c3c; color: white;" onclick="return confirm('Are you sure you want to remove this staff member?')">
                                  <i class="fas fa-user-minus"></i> Remove
                              </button>
                          </form>
                      </div>
                  </div>
                  <div class="staff-details">
                      <div>
                          <small style="color: #7f8c8d;">Email</small><br>
                          <?= $staff['email'] ?>
                      </div>
                      <div>
                          <small style="color: #7f8c8d;">Phone</small><br>
                          <?= $staff['contact'] ?>
                      </div>
                  </div>
                  <div style="font-size: 12px; color: #7f8c8d;">
                      Assigned on: <?= date('M d, Y', strtotime($staff['assignment_date'])) ?>
                  </div>
              </div>
          <?php endforeach; ?>
      <?php else: ?>
          <div class="no-staff">
              <i class="fas fa-users" style="font-size: 48px; margin-bottom: 15px; color: #bdc3c7;"></i>
              <h4>No Staff Assigned</h4>
              <p>This customer doesn't have any staff members assigned yet.</p>
          </div>
      <?php endif; ?>
  </div>