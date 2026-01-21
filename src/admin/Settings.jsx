import React, { useState, useEffect } from 'react'

const Settings = () => {
  const [settings, setSettings] = useState(null)
  const [blocks, setBlocks] = useState([])
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [message, setMessage] = useState(null)

  useEffect(() => {
    if (message) {
      const timer = setTimeout(() => {
        setMessage(null)
      }, 4000)
      return () => clearTimeout(timer)
    }
  }, [message])

  const apiUrl = window.allWooAddonsAdmin.apiUrl
  const nonce = window.allWooAddonsAdmin.nonce

  useEffect(() => {
    fetchSettings()
    fetchBlocks()
  }, [])

  const fetchSettings = async () => {
    try {
      const response = await fetch(`${apiUrl}/settings`, {
        headers: {
          'X-WP-Nonce': nonce
        }
      })

      if (!response.ok) throw new Error('Failed to fetch settings')

      const data = await response.json()
      setSettings(data)
      setLoading(false)
    } catch (err) {
      setMessage({ type: 'error', text: err.message })
      setLoading(false)
    }
  }

  const fetchBlocks = async () => {
    try {
      const response = await fetch(`${apiUrl}/blocks`, {
        headers: {
          'X-WP-Nonce': nonce
        }
      })

      if (!response.ok) throw new Error('Failed to fetch blocks')

      const data = await response.json()
      setBlocks(data)
    } catch (err) {
      setMessage({ type: 'error', text: err.message })
    }
  }

  const handleToggleBlock = (blockId) => {
    setSettings(prev => ({
      ...prev,
      blocks: {
        ...prev.blocks,
        [blockId]: !prev.blocks[blockId]
      }
    }))
  }

  const handleSave = async () => {
    setSaving(true)
    setMessage(null)

    try {
      const response = await fetch(`${apiUrl}/settings`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': nonce
        },
        body: JSON.stringify(settings)
      })

      if (!response.ok) throw new Error('Failed to save settings')

      setMessage({ type: 'success', text: window.allWooAddonsAdmin.strings.saved })
    } catch (err) {
      setMessage({ type: 'error', text: err.message })
    } finally {
      setSaving(false)
    }
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
        <span>Loading settings...</span>
        <style>{`
          @keyframes spin {
            to { transform: rotate(360deg); }
          }
        `}</style>
      </div>
    )
  }

  return (
    <div>
      {message && (
        <div style={{
          padding: '1rem 1.5rem',
          borderRadius: '8px',
          marginBottom: '1rem',
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'space-between',
          backgroundColor: message.type === 'success' ? 'rgba(0, 163, 42, 0.1)' : 'rgba(214, 54, 56, 0.1)',
          border: message.type === 'success' ? '1px solid rgba(0, 163, 42, 0.3)' : '1px solid rgba(214, 54, 56, 0.3)',
          color: message.type === 'success' ? '#00a32a' : '#d63638',
          animation: 'slideDown 0.3s ease'
        }}>
          <span>{message.text}</span>
          <button
            onClick={() => setMessage(null)}
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
        </div>
      )}

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

      <div style={{
        background: 'white',
        borderRadius: '12px',
        padding: '2rem',
        boxShadow: '0 2px 8px rgba(0, 0, 0, 0.08)',
        marginBottom: '2rem',
        border: '1px solid #dcdcde'
      }}>
        <div style={{
          display: 'flex',
          justifyContent: 'space-between',
          alignItems: 'center',
          marginBottom: '1.5rem',
          paddingBottom: '1rem',
          borderBottom: '1px solid #dcdcde'
        }}>
          <h2 style={{
            fontSize: '1.5rem',
            fontWeight: 600,
            margin: 0,
            color: '#1d2327'
          }}>Block Settings</h2>
          <button
            style={{
              background: 'linear-gradient(135deg, #2271b1 0%, #135e96 100%)',
              color: 'white',
              border: 'none',
              padding: '0.75rem 2rem',
              borderRadius: '8px',
              fontSize: '1rem',
              fontWeight: 600,
              cursor: 'pointer',
              transition: 'all 0.3s ease',
              boxShadow: '0 2px 4px rgba(0, 0, 0, 0.1)',
              opacity: saving ? 0.6 : 1
            }}
            onClick={handleSave}
            disabled={saving}
          >
            {saving ? 'Saving...' : window.allWooAddonsAdmin.strings.save}
          </button>
        </div>

        <p style={{ color: '#646970', marginBottom: '1.5rem' }}>
          Enable or disable blocks to control which features are available on your site.
        </p>

        {blocks.map((block) => (
          <div key={block.id} style={{
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'space-between',
            padding: '1.5rem',
            background: 'white',
            border: '1px solid #dcdcde',
            borderRadius: '8px',
            marginBottom: '1rem',
            transition: 'all 0.3s ease'
          }}>
            <div style={{ flex: 1 }}>
              <div style={{
                fontSize: '1.125rem',
                fontWeight: 600,
                color: '#1d2327',
                marginBottom: '0.25rem'
              }}>{block.name}</div>
              <div style={{
                fontSize: '0.875rem',
                color: '#646970'
              }}>{block.description}</div>
            </div>
            <div style={{ marginLeft: '2rem' }}>
              <label style={{
                position: 'relative',
                display: 'inline-block',
                width: '52px',
                height: '28px'
              }}>
                <input
                  type="checkbox"
                  style={{ opacity: 0, width: 0, height: 0 }}
                  checked={settings.blocks[block.id] || false}
                  onChange={() => handleToggleBlock(block.id)}
                />
                <span style={{
                  position: 'absolute',
                  cursor: 'pointer',
                  top: 0,
                  left: 0,
                  right: 0,
                  bottom: 0,
                  backgroundColor: settings.blocks[block.id] ? '#2271b1' : '#ccc',
                  transition: 'all 0.3s ease',
                  borderRadius: '28px'
                }}></span>
                <span style={{
                  position: 'absolute',
                  content: '""',
                  height: '22px',
                  width: '22px',
                  left: settings.blocks[block.id] ? '27px' : '3px',
                  bottom: '3px',
                  backgroundColor: 'white',
                  transition: 'all 0.3s ease',
                  borderRadius: '50%'
                }}></span>
              </label>
            </div>
          </div>
        ))}
      </div>

      <div style={{
        background: 'white',
        borderRadius: '12px',
        padding: '2rem',
        boxShadow: '0 2px 8px rgba(0, 0, 0, 0.08)',
        marginBottom: '2rem',
        border: '1px solid #dcdcde'
      }}>
        <div style={{
          display: 'flex',
          justifyContent: 'space-between',
          alignItems: 'center',
          marginBottom: '1.5rem',
          paddingBottom: '1rem',
          borderBottom: '1px solid #dcdcde'
        }}>
          <h2 style={{
            fontSize: '1.5rem',
            fontWeight: 600,
            margin: 0,
            color: '#1d2327'
          }}>Dashboard Settings</h2>
        </div>

        {[
          { key: 'showRevenue', name: 'Show Revenue', desc: 'Display total revenue on the dashboard' },
          { key: 'showOrders', name: 'Show Orders', desc: 'Display total orders on the dashboard' },
          { key: 'showProducts', name: 'Show Products', desc: 'Display total products on the dashboard' },
          { key: 'showCustomers', name: 'Show Customers', desc: 'Display total customers on the dashboard' }
        ].map((item) => (
          <div key={item.key} style={{
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'space-between',
            padding: '1.5rem',
            background: 'white',
            border: '1px solid #dcdcde',
            borderRadius: '8px',
            marginBottom: '1rem',
            transition: 'all 0.3s ease'
          }}>
            <div style={{ flex: 1 }}>
              <div style={{
                fontSize: '1.125rem',
                fontWeight: 600,
                color: '#1d2327',
                marginBottom: '0.25rem'
              }}>{item.name}</div>
              <div style={{
                fontSize: '0.875rem',
                color: '#646970'
              }}>{item.desc}</div>
            </div>
            <div style={{ marginLeft: '2rem' }}>
              <label style={{
                position: 'relative',
                display: 'inline-block',
                width: '52px',
                height: '28px'
              }}>
                <input
                  type="checkbox"
                  style={{ opacity: 0, width: 0, height: 0 }}
                  checked={settings.dashboard[item.key]}
                  onChange={() => setSettings(prev => ({
                    ...prev,
                    dashboard: { ...prev.dashboard, [item.key]: !prev.dashboard[item.key] }
                  }))}
                />
                <span style={{
                  position: 'absolute',
                  cursor: 'pointer',
                  top: 0,
                  left: 0,
                  right: 0,
                  bottom: 0,
                  backgroundColor: settings.dashboard[item.key] ? '#2271b1' : '#ccc',
                  transition: 'all 0.3s ease',
                  borderRadius: '28px'
                }}></span>
                <span style={{
                  position: 'absolute',
                  content: '""',
                  height: '22px',
                  width: '22px',
                  left: settings.dashboard[item.key] ? '27px' : '3px',
                  bottom: '3px',
                  backgroundColor: 'white',
                  transition: 'all 0.3s ease',
                  borderRadius: '50%'
                }}></span>
              </label>
            </div>
          </div>
        ))}

        <div style={{
          display: 'flex',
          alignItems: 'center',
          justifyContent: 'space-between',
          padding: '1.5rem',
          background: 'white',
          border: '1px solid #dcdcde',
          borderRadius: '8px',
          marginBottom: '1rem',
          transition: 'all 0.3s ease'
        }}>
          <div style={{ flex: 1 }}>
            <div style={{
              fontSize: '1.125rem',
              fontWeight: 600,
              color: '#1d2327',
              marginBottom: '0.25rem'
            }}>Date Range</div>
            <div style={{
              fontSize: '0.875rem',
              color: '#646970'
            }}>Default date range for analytics</div>
          </div>
          <div style={{ marginLeft: '2rem' }}>
            <select
              value={settings.dashboard.dateRange}
              onChange={(e) => setSettings(prev => ({
                ...prev,
                dashboard: { ...prev.dashboard, dateRange: e.target.value }
              }))}
              style={{
                padding: '0.5rem 1rem',
                borderRadius: '6px',
                border: '1px solid #dcdcde',
                fontSize: '0.875rem',
                minWidth: '200px'
              }}
            >
              <option value="7days">Last 7 Days</option>
              <option value="30days">Last 30 Days</option>
              <option value="90days">Last 90 Days</option>
              <option value="1year">Last Year</option>
            </select>
          </div>
        </div>

        <div style={{ marginTop: '1.5rem', textAlign: 'right' }}>
          <button
            style={{
              background: 'linear-gradient(135deg, #2271b1 0%, #135e96 100%)',
              color: 'white',
              border: 'none',
              padding: '0.75rem 2rem',
              borderRadius: '8px',
              fontSize: '1rem',
              fontWeight: 600,
              cursor: 'pointer',
              transition: 'all 0.3s ease',
              boxShadow: '0 2px 4px rgba(0, 0, 0, 0.1)',
              opacity: saving ? 0.6 : 1
            }}
            onClick={handleSave}
            disabled={saving}
          >
            {saving ? 'Saving...' : window.allWooAddonsAdmin.strings.save}
          </button>
        </div>
      </div>
    </div>
  )
}

export default Settings
