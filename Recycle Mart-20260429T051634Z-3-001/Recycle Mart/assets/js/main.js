document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. Sticky Header Shadow on Scroll ---
    const header = document.querySelector('.site-header');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                header.style.boxShadow = '0 4px 20px rgba(0,0,0,0.05)';
            } else {
                header.style.boxShadow = 'none';
            }
        });
    }

    // --- 2. Premium Off-Canvas Mobile Menu Logic ---
    const mobileToggleBtn = document.querySelector('.mobile-toggle');
    const mobileCloseBtn = document.querySelector('.mobile-close');
    const mainNav = document.querySelector('.main-nav');
    const mobileOverlay = document.querySelector('.mobile-overlay');

    const openMobileMenu = () => {
        mainNav.classList.add('active');
        mobileOverlay.classList.add('active');
        document.body.style.overflow = 'hidden'; 
    }

    const closeMobileMenu = () => {
        mainNav.classList.remove('active');
        mobileOverlay.classList.remove('active');
        document.body.style.overflow = ''; 
    }

    if (mobileToggleBtn && mainNav) {
        mobileToggleBtn.addEventListener('click', openMobileMenu);
        mobileCloseBtn.addEventListener('click', closeMobileMenu);
        mobileOverlay.addEventListener('click', closeMobileMenu); 
    }

    // --- 3. DYNAMIC SHOPPING CART LOGIC ---
    let rmCart = JSON.parse(localStorage.getItem('recycle_mart_cart')) || [];

    const floatingCartBtn = document.getElementById('floating-cart');
    const headerCartBtn = document.getElementById('header-cart-btn');
    const cartDrawer = document.getElementById('cart-drawer');
    const cartOverlay = document.getElementById('cart-drawer-overlay');
    const closeCartBtn = document.getElementById('close-drawer');
    const continueShoppingBtn = document.getElementById('continue-shopping-btn');
    
    const cartItemsContainer = document.getElementById('drawer-items');
    const cartBadgeCount = document.getElementById('cart-badge-count');
    const cartSubtotalAmount = document.getElementById('cart-subtotal-amount');

    const openCartDrawer = () => {
        if(cartDrawer && cartOverlay) {
            cartDrawer.classList.add('active');
            cartOverlay.classList.add('active');
            document.body.style.overflow = 'hidden'; 
        }
    };

    const closeCartDrawer = () => {
        if(cartDrawer && cartOverlay) {
            cartDrawer.classList.remove('active');
            cartOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    };

    if(floatingCartBtn) floatingCartBtn.addEventListener('click', openCartDrawer);
    if(headerCartBtn) headerCartBtn.addEventListener('click', openCartDrawer);
    if(closeCartBtn) closeCartBtn.addEventListener('click', closeCartDrawer);
    if(cartOverlay) cartOverlay.addEventListener('click', closeCartDrawer);
    if(continueShoppingBtn) continueShoppingBtn.addEventListener('click', closeCartDrawer);

    const saveCart = () => {
        localStorage.setItem('recycle_mart_cart', JSON.stringify(rmCart));
        renderCartUI();
    };

    const addToCart = (product) => {
        let existingItem = rmCart.find(item => item.id === product.id);
        let qtyToAdd = product.qtyToAdd || 1;
        
        if (existingItem) {
            existingItem.qty += qtyToAdd;
        } else {
            rmCart.push({ 
                id: product.id, 
                title: product.title, 
                price: product.price, 
                img: product.img, 
                qty: qtyToAdd 
            });
        }
        saveCart();
        openCartDrawer(); 
    };

    const updateQty = (id, changeAmount) => {
        let item = rmCart.find(i => i.id === id);
        if (item) {
            item.qty += changeAmount;
            if (item.qty <= 0) {
                rmCart = rmCart.filter(i => i.id !== id);
            }
            saveCart();
        }
    };

    const removeItem = (id) => {
        rmCart = rmCart.filter(i => i.id !== id);
        saveCart();
    };

    const renderCartUI = () => {
        let totalItems = 0;
        let subtotal = 0;
        let htmlContent = '';

        if (rmCart.length === 0) {
            htmlContent = `<div class="empty-cart-msg">Your cart is currently empty. Add some materials!</div>`;
        } else {
            rmCart.forEach(item => {
                totalItems += item.qty;
                subtotal += (item.price * item.qty);
                
                // UPDATED: Links now point to .php
                htmlContent += `
                    <div class="cart-item-row">
                        <a href="product-details.php?id=${item.id}">
                            <img src="${item.img}" alt="${item.title}" class="cart-item-img">
                        </a>
                        <div class="cart-item-info">
                            <a href="product-details.php?id=${item.id}" style="text-decoration:none;">
                                <div class="cart-item-title">${item.title}</div>
                            </a>
                            
                            <div class="cart-item-price-remove">
                                <div class="cart-item-price">Tk. ${item.price.toFixed(2)}</div>
                                <button class="remove-btn" data-id="${item.id}">
                                    <i class="fa-solid fa-trash" style="pointer-events:none;"></i> Remove
                                </button>
                            </div>
                            
                            <div class="cart-qty-controls">
                                <button class="qty-action decrease-qty" data-id="${item.id}">
                                    <i class="fa-solid fa-minus" style="pointer-events:none;"></i>
                                </button>
                                <span class="qty-amount">${item.qty}</span>
                                <button class="qty-action increase-qty" data-id="${item.id}">
                                    <i class="fa-solid fa-plus" style="pointer-events:none;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        if(cartItemsContainer) cartItemsContainer.innerHTML = htmlContent;
        if(cartBadgeCount) cartBadgeCount.innerText = totalItems;
        if(cartSubtotalAmount) cartSubtotalAmount.innerText = subtotal.toFixed(2);

        bindDrawerEvents();
    };

    const bindDrawerEvents = () => {
        document.querySelectorAll('.increase-qty').forEach(btn => {
            btn.addEventListener('click', (e) => {
                updateQty(e.target.dataset.id, 1);
            });
        });
        document.querySelectorAll('.decrease-qty').forEach(btn => {
            btn.addEventListener('click', (e) => {
                updateQty(e.target.dataset.id, -1);
            });
        });
        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                removeItem(e.target.dataset.id);
            });
        });
    };

    renderCartUI();

    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); 
            let qty = 1;
            if (this.id === 'detail-add-to-cart') {
                const qtyInput = document.getElementById('detail-qty-input');
                if (qtyInput) {
                    qty = parseInt(qtyInput.value) || 1;
                }
            }
            let productData = {
                id: this.dataset.id,
                title: this.dataset.title,
                price: parseFloat(this.dataset.price),
                img: this.dataset.img,
                qtyToAdd: qty
            };
            if(productData.id) addToCart(productData);
        });
    });

    // --- 4. DYNAMIC PRODUCT DETAILS PAGE LOGIC ---
    const productsDatabase = [
        { 
            id: "101", 
            title: "Used Cardboard Boxes", 
            price: 120, 
            unit: "/ Bundle", 
            img: "assets/images/products/rm-listings-cardboard-boxes.png", 
            tag: "Featured",
            tagClass: "tag-featured",
            gallery: [
                "assets/images/products/rm-listings-cardboard-boxes.png",
                "assets/images/products/rm-listings-cardboard-boxes-2.png",
                "assets/images/products/rm-listings-cardboard-boxes-3.png",
                "assets/images/products/rm-listings-cardboard-boxes-4.png"
            ],
            condition: "Bulk Quantity", 
            location: "Dhaka, Bangladesh", 
            desc: "High-quality used cardboard boxes available in bulk. Completely flattened and sorted. Perfect for packaging, moving, or recycling plants looking for clean corrugated material." 
        },
        { 
            id: "102", 
            title: "Plastic Drums (50L)", 
            price: 250, 
            unit: "/ Piece", 
            img: "assets/images/products/rm-listings-plastic-drums.png",
            tag: "New",
            tagClass: "tag-new",
            gallery: [
                "assets/images/products/rm-listings-plastic-drums.png",
                "assets/images/products/rm-listings-plastic-drums-2.png",
                "assets/images/products/rm-listings-plastic-drums-3.png",
                "assets/images/products/rm-listings-plastic-drums-4.png"
            ], 
            condition: "Good Condition", 
            location: "Chittagong, Bangladesh", 
            desc: "Clean, reusable 50L plastic drums. Previously used for safe, non-toxic materials. Excellent for water storage, DIY planters, or industrial upcycling." 
        },
        { 
            id: "103", 
            title: "Copper Wires", 
            price: 560, 
            unit: "/ Kg", 
            img: "assets/images/products/rm-listings-copper-wires.png", 
            tag: "Featured",
            tagClass: "tag-featured",
            gallery: [
                "assets/images/products/rm-listings-copper-wires.png",
                "assets/images/products/rm-listings-copper-wires-2.png",
                "assets/images/products/rm-listings-copper-wires-3.png",
                "assets/images/products/rm-listings-copper-wires-4.png"
            ],
            condition: "Clean & Sorted", 
            location: "Gazipur, Bangladesh", 
            desc: "High-quality, stripped copper wires available in bulk. Completely clean, sorted, and free from insulation or heavy oxidation. Perfect for recycling plants or metal casting projects." 
        },
        { 
            id: "104", 
            title: "Wooden Pallets", 
            price: 300, 
            unit: "/ Piece", 
            img: "assets/images/products/rm-listings-wooden-pallets.png",
            tag: "New",
            tagClass: "tag-new",
            gallery: [
                "assets/images/products/rm-listings-wooden-pallets.png",
                "assets/images/products/rm-listings-wooden-pallets-2.png",
                "assets/images/products/rm-listings-wooden-pallets-3.png",
                "assets/images/products/rm-listings-wooden-pallets-4.png"
            ], 
            condition: "Reusable Condition", 
            location: "Dhaka, Bangladesh", 
            desc: "Sturdy wooden pallets suitable for warehouse storage, shipping, or upcycling furniture projects. Heat-treated and inspected for structural integrity." 
        },
        { 
            id: "105", 
            title: "Used Monitors", 
            price: 800, 
            unit: "/ Piece", 
            img: "assets/images/products/rm-listings-used-monitors.png", 
            tag: "Hot",
            tagClass: "tag-hot",
            gallery: [
                "assets/images/products/rm-listings-used-monitors.png",
                "assets/images/products/rm-listings-used-monitors-2.png",
                "assets/images/products/rm-listings-used-monitors-3.png",
                "assets/images/products/rm-listings-used-monitors-4.png"
            ],
            condition: "Working Condition", 
            location: "Sylhet, Bangladesh", 
            desc: "Batch of used but fully functional LCD and LED monitors. Great for repair shops, affordable office setups, or e-waste recycling processing." 
        }
    ];

    // UPDATED: Now looks for .php in the URL
    if (window.location.pathname.includes('product-details.php')) {
        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('id');
        const product = productsDatabase.find(p => p.id === productId);

        if (product) {
            document.title = `${product.title} | Recycle Mart BD`;
            document.getElementById('detail-breadcrumb-title').innerText = product.title;
            document.getElementById('detail-title').innerText = product.title;
            
            const mainImgElement = document.getElementById('detail-main-img');
            mainImgElement.src = product.img;
            
            const tagEl = document.getElementById('detail-tag');
            tagEl.innerText = product.tag;
            tagEl.className = `tag ${product.tagClass}`;

            let galleryHtml = '';
            product.gallery.forEach((imgSrc, index) => {
                let activeClass = index === 0 ? 'active' : '';
                galleryHtml += `<img src="${imgSrc}" class="thumb ${activeClass}" alt="Gallery ${index+1}">`;
            });
            document.getElementById('detail-thumbnails').innerHTML = galleryHtml;
            
            const thumbs = document.querySelectorAll('.thumb');
            thumbs.forEach(thumb => {
                thumb.addEventListener('click', function() {
                    thumbs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    if(this.tagName === 'IMG') {
                        mainImgElement.src = this.src;
                        mainImgElement.style.transform = 'scale(1)';
                    }
                });
            });
            
            const zoomContainer = document.getElementById('img-zoom-container');
            if (zoomContainer && mainImgElement) {
                zoomContainer.addEventListener('mousemove', (e) => {
                    const { left, top, width, height } = zoomContainer.getBoundingClientRect();
                    const x = ((e.clientX - left) / width) * 100;
                    const y = ((e.clientY - top) / height) * 100;
                    mainImgElement.style.transformOrigin = `${x}% ${y}%`;
                    mainImgElement.style.transform = 'scale(2)';
                });
                zoomContainer.addEventListener('mouseleave', () => {
                    mainImgElement.style.transformOrigin = 'center center';
                    mainImgElement.style.transform = 'scale(1)';
                });
            }

            const detailMinus = document.getElementById('detail-qty-minus');
            const detailPlus = document.getElementById('detail-qty-plus');
            const detailInput = document.getElementById('detail-qty-input');

            if (detailMinus && detailPlus && detailInput) {
                detailMinus.addEventListener('click', () => {
                    let val = parseInt(detailInput.value) || 1;
                    if (val > 1) detailInput.value = val - 1;
                });
                detailPlus.addEventListener('click', () => {
                    let val = parseInt(detailInput.value) || 1;
                    detailInput.value = val + 1;
                });
            }

            document.getElementById('detail-condition').innerText = product.condition;
            document.getElementById('detail-location-text').innerText = product.location;
            document.getElementById('detail-price').innerText = product.price.toFixed(2);
            document.getElementById('detail-unit').innerText = product.unit;
            document.getElementById('detail-desc').innerText = product.desc;
            
            const detailsCartBtn = document.getElementById('detail-add-to-cart');
            if(detailsCartBtn) {
                detailsCartBtn.dataset.id = product.id;
                detailsCartBtn.dataset.title = product.title;
                detailsCartBtn.dataset.price = product.price;
                detailsCartBtn.dataset.img = product.img;
            }
        } else {
            document.getElementById('detail-title').innerText = "Product Not Found";
        }
    }


    // --- 5. DYNAMIC CHECKOUT PAGE LOGIC ---
    // UPDATED: Now looks for checkout.php in the URL
    if (window.location.pathname.includes('checkout.php')) {
        
        const checkoutItemsContainer = document.getElementById('checkout-order-items');
        const checkoutSubtotal = document.getElementById('checkout-subtotal');
        const checkoutTotal = document.getElementById('checkout-total');
        const checkoutForm = document.getElementById('paymentForm');
        
        const shippingFee = 150;
        const platformFee = 50;

        const renderCheckoutSummary = () => {
            let subtotal = 0;
            let htmlContent = '';

            if (rmCart.length === 0) {
                htmlContent = `<p style="text-align:center; color:var(--text-muted); padding: 20px;">Your cart is empty.</p>`;
                if (checkoutForm) {
                    document.getElementById('process-order-btn').disabled = true;
                    document.getElementById('process-order-btn').innerText = "Cart is Empty";
                }
            } else {
                rmCart.forEach(item => {
                    let itemTotal = item.price * item.qty;
                    subtotal += itemTotal;
                    
                    // UPDATED: Links point to .php
                    htmlContent += `
                        <div class="cart-item">
                            <a href="product-details.php?id=${item.id}">
                                <img src="${item.img}" alt="${item.title}">
                            </a>
                            <div class="item-details">
                                <a href="product-details.php?id=${item.id}" style="color:inherit; text-decoration:none;">
                                    <h4>${item.title}</h4>
                                </a>
                                <p>Qty: ${item.qty}</p>
                            </div>
                            <div class="item-price">Tk. ${itemTotal.toFixed(2)}</div>
                        </div>
                    `;
                });
            }

            if (checkoutItemsContainer) checkoutItemsContainer.innerHTML = htmlContent;
            if (checkoutSubtotal) checkoutSubtotal.innerText = subtotal.toFixed(2);
            
            if (checkoutTotal) {
                let grandTotal = subtotal > 0 ? (subtotal + shippingFee + platformFee) : 0;
                checkoutTotal.innerText = grandTotal.toFixed(2);
            }
        };

        renderCheckoutSummary();

        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (rmCart.length === 0) {
                    alert("Your cart is empty!");
                    return;
                }

                const ccInput = document.getElementById('checkout-cc-num');
                const ccValue = ccInput ? ccInput.value.replace(/\s+/g, '') : '';
                const btn = document.getElementById('process-order-btn');

                if (ccValue === '4242424242424242') {
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
                    btn.disabled = true;
                    
                    setTimeout(() => {
                        rmCart = [];
                        localStorage.setItem('recycle_mart_cart', JSON.stringify(rmCart));
                        document.getElementById('checkout-main-view').classList.add('hidden');
                        document.getElementById('checkout-success-view').classList.remove('hidden');
                        renderCartUI();
                    }, 1500);
                } else {
                    alert("Payment Failed. Please use the Test Card: 4242 4242 4242 4242");
                }
            });
        }
    }

});