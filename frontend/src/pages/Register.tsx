import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { useAuth } from "@/context/AuthContext";
import { useConfig } from "@/context/ConfigContext";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Label } from "@/components/ui/label";
import { Alert, AlertDescription } from "@/components/ui/alert";
import { AnimatedBackground } from "@/components/aceternity/AnimatedBackground";
import { motion } from "framer-motion";

const schema = z
  .object({
    username: z.string().min(1, "Required"),
    password: z.string().min(6, "At least 6 characters"),
    password_confirmation: z.string(),
  })
  .refine((d) => d.password === d.password_confirmation, {
    message: "Passwords must match",
    path: ["password_confirmation"],
  });

type FormData = z.infer<typeof schema>;

const Register = () => {
  const navigate = useNavigate();
  const { registerStep, loading } = useAuth();
  const { appConfig, loading: configLoading } = useConfig();
  const [error, setError] = useState<string | null>(null);
  // Match backend: use register_method from config. When config is missing, default to email so
  // email-only backends still show the correct form even if config request failed.
  const isEmail =
    appConfig == null ? true : appConfig.register_method === "email";

  const {
    register: reg,
    handleSubmit,
    formState: { errors },
  } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: {
      username: "",
      password: "",
      password_confirmation: "",
    },
  });

  const onSubmit = async (data: FormData) => {
    setError(null);
    const body: Record<string, unknown> = {
      password: data.password,
      password_confirmation: data.password_confirmation,
    };
    if (isEmail) {
      body.email = data.username.trim();
    } else {
      body.mobile = data.username.replace(/\D/g, "");
      body.country_code = "+91";
    }
    const result = await registerStep(1, body);
    if (result.ok && !result.message) navigate("/", { replace: true });
    else if (result.ok && result.message) setError(result.message);
    else setError(result.message ?? "Registration failed");
  };

  return (
    <AnimatedBackground variant="grid">
      <motion.div
        initial={{ opacity: 0, y: 12 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.4 }}
        className="w-full max-w-md px-4"
      >
        <Card className="w-full border-border/80 bg-card/90 shadow-elevated backdrop-blur-md">
          <CardHeader className="space-y-1 text-center">
            <CardTitle className="text-2xl font-display">Create account</CardTitle>
            <CardDescription>
              Register with {isEmail ? "email" : "mobile"} and password
            </CardDescription>
          </CardHeader>
          <CardContent>
            {configLoading && (
              <p className="text-sm text-muted-foreground text-center py-4">
                Loading registration options…
              </p>
            )}
            {!configLoading && (
            <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
              {error && (
                error.toLowerCase().includes("verify") ? (
                  <Alert>
                    <AlertDescription className="space-y-2">
                      <span className="block">{error}</span>
                      <span className="block text-sm text-muted-foreground">
                        If you don&apos;t see the email, check your spam folder. In local development (no SMTP configured), the verification code is written to the server log file: <code className="text-xs bg-muted px-1 rounded">storage/logs/laravel.log</code>
                      </span>
                      <div className="flex flex-wrap gap-2 mt-2">
                        <Link to="/verify" className="font-medium underline">
                          Enter verification code
                        </Link>
                        <span className="text-muted-foreground">·</span>
                        <Link to="/login" className="font-medium underline">
                          Sign in
                        </Link>
                      </div>
                    </AlertDescription>
                  </Alert>
                ) : (
                  <Alert variant="destructive">
                    <AlertDescription>{error}</AlertDescription>
                  </Alert>
                )
              )}
              <div className="space-y-2">
                <Label htmlFor="username">
                  {isEmail ? "Email" : "Mobile"}
                </Label>
                <Input
                  id="username"
                  type={isEmail ? "email" : "text"}
                  placeholder={isEmail ? "you@example.com" : "Mobile number"}
                  autoComplete={isEmail ? "email" : "tel"}
                  className="transition-shadow focus-visible:ring-2"
                  {...reg("username")}
                />
                {errors.username && (
                  <p className="text-sm text-destructive">
                    {errors.username.message}
                  </p>
                )}
              </div>
              <div className="space-y-2">
                <Label htmlFor="password">Password</Label>
                <Input
                  id="password"
                  type="password"
                  autoComplete="new-password"
                  className="transition-shadow focus-visible:ring-2"
                  {...reg("password")}
                />
                {errors.password && (
                  <p className="text-sm text-destructive">
                    {errors.password.message}
                  </p>
                )}
              </div>
              <div className="space-y-2">
                <Label htmlFor="password_confirmation">Confirm password</Label>
                <Input
                  id="password_confirmation"
                  type="password"
                  autoComplete="new-password"
                  className="transition-shadow focus-visible:ring-2"
                  {...reg("password_confirmation")}
                />
                {errors.password_confirmation && (
                  <p className="text-sm text-destructive">
                    {errors.password_confirmation.message}
                  </p>
                )}
              </div>
              <Button
                type="submit"
                className="w-full bg-gradient-cta text-primary-foreground shadow-md hover:opacity-90 transition-opacity"
                disabled={loading}
              >
                {loading ? "Creating account…" : "Register"}
              </Button>
            </form>
            )}
            <p className="mt-4 text-center text-sm text-muted-foreground">
              Already have an account?{" "}
              <Link to="/login" className="text-primary font-medium hover:underline">
                Sign in
              </Link>
            </p>
          </CardContent>
        </Card>
      </motion.div>
    </AnimatedBackground>
  );
};

export default Register;
