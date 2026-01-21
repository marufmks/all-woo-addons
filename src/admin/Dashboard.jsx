import React, { useState, useEffect } from 'react'

const styles = {
  statCard: {
    background: 'white',
    borderRadius: '12px',
    padding: '1.5rem',
    boxShadow: '0 2px 8px rgba(0, 0, 0, 0.08)',
    transition: 'all 0.3s ease',
    border: '1px solid #dcdcde'
  },
  statIcon: {
    width: '48px',
    height: '48px',
    borderRadius: '12px',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    fontSize: '1.5rem',
    marginBottom: '1rem'
  },
  statLabel: {
    fontSize: '0.875rem',
    color: '#646970',
    marginBottom: '0.5rem',
    fontWeight: 500
  },
  statValue: {
    fontSize: '2rem',
    fontWeight: 700,
    color: '#1d2327',
    lineHeight: 1.2
  },
  section: {
    background: 'white',
    borderRadius: '12px',
    padding: '2rem',
    boxShadow: '0 2px 8px rgba(0, 0, 0, 0.08)',
    marginBottom: '2rem',
    border: '1px solid #dcdcde'
  },
  sectionHeader: {
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: '1.5rem',
    paddingBottom: '1rem',
    borderBottom: '1px solid #dcdcde'
  },
  sectionTitle: {
    fontSize: '1.5rem',
    fontWeight: 600,
    margin: 0,
    color: '#1d2327'
  },
  table: {
    width: '100%',
    borderCollapse: 'collapse'
  },
  tableCell: {
    padding: '1rem',
    textAlign: 'left',
    borderBottom: '1px solid #dcdcde'
  },
  tableHeader: {
    fontWeight: 600,
    color: '#646970',
    fontSize: '0.875rem',
    textTransform: 'uppercase',
    letterSpacing: '0.5px'
  },
  badge: {
    display: 'inline-block',
    padding: '0.25rem 0.75rem',
    borderRadius: '12px',
    fontSize: '0.75rem',
    fontWeight: 600,
    textTransform: 'uppercase'
  },
  dashboard: {}
}

const Dashboard = () => {
  const [stats, setStats] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)

  const apiUrl = window.allWooAddonsAdmin.apiUrl
  const nonce = window.allWooAddonsAdmin.nonce

  useEffect(() => {
    if (error) {
      const timer = setTimeout(() => {
        setError(null)
      }, 5000)
      return () => clearTimeout(timer)
    }
  }, [error])

  useEffect(() => {
    fetchStats()
  }, [])

  const fetchStats = async () => {
    try {
      console.log('Fetching stats from:', `${apiUrl}/stats`)
      const response = await fetch(`${apiUrl}/stats`, {
        headers: {
          'X-WP-Nonce': nonce
        }
      })

      console.log('Response status:', response.status)
      console.log('Response ok:', response.ok)

      const responseText = await response.text()
      console.log('Response text:', responseText)

      if (!response.ok) throw new Error('Failed to fetch stats')

      const data = JSON.parse(responseText)
      console.log('Parsed data:', data)
      setStats(data)
      setLoading(false)
    } catch (err) {
      console.error('Error fetching stats:', err)
      setError(err.message)
      setLoading(false)
    }
  }

  const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD'
    }).format(amount)
  }

  const formatNumber = (num) => {
    return new Intl.NumberFormat('en-US').format(num)
  }

  if (loading) {
    return (
      <div style={{
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        padding: '4rem',
        color: '#646970'
      }}>
        <div style={{
          width: '40px',
          height: '40px',
          border: '3px solid #dcdcde',
          borderTopColor: '#2271b1',
          borderRadius: '50%',
          animation: 'spin 0.8s linear infinite',
          marginRight: '1rem'
        }}></div>
        <span>Loading stats...</span>
        <style>{`
          @keyframes spin {
            to { transform: rotate(360deg); }
          }
        `}</style>
      </div>
    )
  }

  if (error) {
    return (
      <div style={{
        padding: '1rem 1.5rem',
        borderRadius: '8px',
        marginBottom: '1rem',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'space-between',
        backgroundColor: 'rgba(214, 54, 56, 0.1)',
        border: '1px solid rgba(214, 54, 56, 0.3)',
        color: '#d63638',
        animation: 'slideDown 0.3s ease'
      }}>
        <span><strong>Error:</strong> {error}</span>
        <button
          onClick={() => setError(null)}
          style={{
            background: 'none',
            border: 'none',
            color: 'inherit',
            cursor: 'pointer',
            fontSize: '1.2rem',
            lineHeight: 1,
            padding: '0',
            marginLeft: '1rem',
            opacity: 0.7,
            transition: 'opacity 0.2s ease'
          }}
          onMouseEnter={(e) => e.target.style.opacity = '1'}
          onMouseLeave={(e) => e.target.style.opacity = '0.7'}
          aria-label="Close notification"
        >
          ✕
        </button>
        <style>{`
          @keyframes slideDown {
            from {
              opacity: 0;
              transform: translateY(-10px);
            }
            to {
              opacity: 1;
              transform: translateY(0);
            }
          }
        `}</style>
      </div>
    )
  }

  return (
    <div className="awa-dashboard" style={{
      ...styles.dashboard
    }}>
      <div className="awa-stats-grid" style={{
        display: 'grid',
        gap: '1.5rem',
        gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))',
        marginBottom: '2rem'
      }}>
        <div style={styles.statCard}>
          <div style={{...styles.statIcon, backgroundColor: 'rgba(34, 113, 177, 0.1)', color: '#2271b1'}}>
            💰
          </div>
          <div style={styles.statLabel}>Total Revenue</div>
          <div style={styles.statValue}>{formatCurrency(stats.totalRevenue)}</div>
        </div>

        <div style={styles.statCard}>
          <div style={{...styles.statIcon, backgroundColor: 'rgba(0, 163, 42, 0.1)', color: '#00a32a'}}>
            📦
          </div>
          <div style={styles.statLabel}>Total Orders</div>
          <div style={styles.statValue}>{formatNumber(stats.totalOrders)}</div>
        </div>

        <div style={styles.statCard}>
          <div style={{...styles.statIcon, backgroundColor: 'rgba(219, 166, 23, 0.1)', color: '#dba617'}}>
            🛍️
          </div>
          <div style={styles.statLabel}>Products</div>
          <div style={styles.statValue}>{formatNumber(stats.totalProducts)}</div>
        </div>

        <div style={styles.statCard}>
          <div style={{...styles.statIcon, backgroundColor: 'rgba(114, 174, 230, 0.1)', color: '#72aee6'}}>
            👥
          </div>
          <div style={styles.statLabel}>Customers</div>
          <div style={styles.statValue}>{formatNumber(stats.totalCustomers)}</div>
        </div>

        <div style={styles.statCard}>
          <div style={{...styles.statIcon, backgroundColor: 'rgba(214, 54, 56, 0.1)', color: '#d63638'}}>
            📊
          </div>
          <div style={styles.statLabel}>Avg. Order Value</div>
          <div style={styles.statValue}>
            {formatCurrency(stats.averageOrderValue)}
          </div>
        </div>
      </div>

      <div style={styles.section}>
        <div style={styles.sectionHeader}>
          <h2 style={styles.sectionTitle}>Recent Orders</h2>
        </div>
        <table style={styles.table}>
          <thead>
            <tr>
              <th style={styles.tableHeader}>Order #</th>
              <th style={styles.tableHeader}>Customer</th>
              <th style={styles.tableHeader}>Status</th>
              <th style={styles.tableHeader}>Total</th>
              <th style={styles.tableHeader}>Date</th>
            </tr>
          </thead>
          <tbody>
            {stats.recentOrders && stats.recentOrders.map((order) => (
              <tr key={order.id}>
                <td style={styles.tableCell}>#{order.number}</td>
                <td style={styles.tableCell}>{order.customer}</td>
                <td style={styles.tableCell}>
                  <span style={{
                    ...styles.badge,
                    backgroundColor: order.status === 'completed' ? 'rgba(0, 163, 42, 0.1)' : 
                                   order.status === 'processing' ? 'rgba(34, 113, 177, 0.1)' :
                                   'rgba(219, 166, 23, 0.1)',
                    color: order.status === 'completed' ? '#00a32a' : 
                             order.status === 'processing' ? '#2271b1' :
                             '#dba617'
                  }}>
                    {order.status}
                  </span>
                </td>
                <td style={styles.tableCell}>{formatCurrency(order.total)}</td>
                <td style={styles.tableCell}>{new Date(order.date).toLocaleDateString()}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <div style={styles.section}>
        <div style={styles.sectionHeader}>
          <h2 style={styles.sectionTitle}>Top Products</h2>
        </div>
        <table style={styles.table}>
          <thead>
            <tr>
              <th style={styles.tableHeader}>Product</th>
              <th style={styles.tableHeader}>Units Sold</th>
            </tr>
          </thead>
          <tbody>
            {stats.topProducts && stats.topProducts.map((product) => (
              <tr key={product.id}>
                <td style={styles.tableCell}>{product.name}</td>
                <td style={styles.tableCell}>{formatNumber(product.sold)}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <div style={styles.section}>
        <div style={styles.sectionHeader}>
          <h2 style={styles.sectionTitle}>Revenue Trend</h2>
        </div>
        {stats.revenueByMonth && (
          <table style={styles.table}>
            <thead>
              <tr>
                <th style={styles.tableHeader}>Month</th>
                <th style={styles.tableHeader}>Revenue</th>
              </tr>
            </thead>
            <tbody>
              {stats.revenueByMonth.map((item) => (
                <tr key={item.month}>
                  <td style={styles.tableCell}>{item.month}</td>
                  <td style={styles.tableCell}>{formatCurrency(item.revenue)}</td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>
    </div>
  )
}

export default Dashboard
