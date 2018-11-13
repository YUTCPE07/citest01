
						<?php if ($username === 'sally'): ?>

						        <h3>Hi Sally</h3>

						<?php else: ?>
								<h3>Hi unknown user</h3>
								<?php if ($username === '3.5'): ?>
						        	<h3>Hi 3.5</h3>
					        	<?php else: ?>
					        		<h3>Hi 00</h3>
			        			<?php endif;?>
						<?php endif;?>

SELECT * FROM ( SELECT coup_CouponID, coup_Name, coup_ImagePath,coup_Image, coup_Price,coup_Description,coup_UpdatedDate, path_logo, logo_image, category_brand,mi_brand.flag_status,mi_brand.flag_del,mi_brand.flag_hidden FROM hilight_coupon LEFT JOIN mi_brand ON hilight_coupon.coup_CouponID=mi_brand.brand_Id UNION ALL SELECT card_id,mi_card.name,path_image,image,member_price,description,greeting_updateddate, path_logo, logo_image, category_brand,mi_brand.flag_status,mi_brand.flag_del,mi_brand.flag_hidden FROM mi_card LEFT JOIN mi_brand ON mi_card.brand_id=mi_brand.brand_Id ) AS U WHERE U.flag_status = 1 AND U.flag_del = 0 AND U.flag_hidden = 'No' ORDER BY coup_UpdatedDate DESC


SELECT * FROM (
					SELECT 	hilight_coupon.coup_CouponID,
							hilight_coupon.coup_Name,
							hilight_coupon.coup_ImagePath,
							hilight_coupon.coup_Image,
							hilight_coupon.coup_Price,coup_Description,
							hilight_coupon.coup_UpdatedDate,
							hilight_coupon.path_logo,
							hilight_coupon.logo_image,
							hilight_coupon.category_brand,
							mi_brand.flag_status,
							mi_brand.flag_del,
							mi_brand.flag_hidden
					FROM hilight_coupon LEFT JOIN mi_brand ON hilight_coupon.coup_CouponID=mi_brand.brand_Id
					UNION ALL
					SELECT card_id,
					mi_card.name,
					mi_card.path_image,
					mi_card.image,
					mi_card.member_price,
					mi_card.description,
					mi_card.greeting_updateddate,
					mi_card.path_logo,
					mi_card.logo_image,
					mi_card.category_brand,
					mi_brand.flag_status,
					mi_brand.flag_del,
					mi_brand.flag_hidden FROM mi_card LEFT JOIN mi_brand ON mi_card.brand_id=mi_brand.brand_Id
				) AS U
				WHERE 	U.flag_status = 1 AND
						U.flag_del = 0 AND
						U.flag_hidden = 'No'
				ORDER BY coup_UpdatedDate DESC