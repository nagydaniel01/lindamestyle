                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td align="center" style="padding:20px; background-color:#f3f3f3; font-size:12px; color:#999999;">
                                <p>
                                    <?php echo esc_html__( 'You received this letter because this address was registered for our service.', TEXT_DOMAIN ); ?>
                                </p>
                                <a href="<?php echo esc_url(home_url()); ?>" style="color:#999999; text-decoration:none;">
                                    <?php echo esc_html(get_bloginfo('name')); ?>
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>